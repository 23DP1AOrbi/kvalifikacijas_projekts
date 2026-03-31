<template>
  <v-container>
    <v-btn @click="$router.back()" class="mb-4">Back</v-btn>

    <v-card v-if="svgContent">
      <v-card-title>{{ design?.name }}</v-card-title>

      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-color-picker
              v-model="selectedColor"
              hide-inputs
              flat
              class="mb-4"
            ></v-color-picker>

            <div v-if="usedColors.length > 0" class="mb-4">
              <div class="text-subtitle-2 mb-2 text-grey-darken-1">Recently Used</div>
              <div class="d-flex flex-wrap gap-2">
                <div
                  v-for="color in usedColors"
                  :key="color"
                  class="color-swatch"
                  :style="{ backgroundColor: color }"
                  @click="selectedColor = color"
                  :title="color"
                ></div>
              </div>
            </div>

            <v-divider class="mb-4"></v-divider>

            <v-btn @click="undo" class="mr-2" :disabled="undoStack.length === 0">Undo</v-btn>
            <v-btn @click="redo" class="mr-2" :disabled="redoStack.length === 0">Redo</v-btn>
            <v-btn @click="reset" color="error" variant="tonal">Reset</v-btn>
          </v-col>

          <v-col cols="12" md="8" class="d-flex justify-center">
            <div ref="svgContainer" v-html="svgContent" class="svg-large"></div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div v-else>Loading...</div>
  </v-container>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "../bootstrap.js";

const route = useRoute();
const design = ref(null);
const svgContent = ref('');
const svgContainer = ref(null);

const selectedColor = ref('#ff0000');

// NEW: Color history state
const usedColors = ref([]);
const MAX_HISTORY = 12; // Keep the palette clean

// Per-element color state
const colors = ref({});

// Undo/redo stacks
const undoStack = ref([]);
const redoStack = ref([]);

// Fetch design
const fetchDesign = async () => {
  try {
    const res = await axios.get(`/api/dizaini/${route.params.id}`);
    design.value = res.data;

    // Use a cache-buster or ensure the URL is clean
    const svgRes = await axios.get(design.value.file_url, {
      responseType: 'text' // Force text response
    });

    let rawSvg = svgRes.data;

    // CLEANUP: If the file contains <?xml ... ?> or <!DOCTYPE ... ?>, 
    // it can break v-html injection. We only want the <svg>...</svg> part.
    const svgStartIndex = rawSvg.indexOf('<svg');
    if (svgStartIndex !== -1) {
      rawSvg = rawSvg.substring(svgStartIndex);
    }

    svgContent.value = rawSvg;
  } catch (err) {
    console.error("Fetch Error:", err);
  }
};

const getColorableElements = (svg) => {
  // Query for all shapes that can typically be colored
  return svg.querySelectorAll("path, circle, rect, ellipse, polygon, polyline");
};

const setupSvgInteractions = async () => {
  await nextTick();
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const elements = getColorableElements(svg);

  elements.forEach((el, index) => {
    if (!el.id) {
      el.id = `svg-shape-${index}`;
    }

    // NEW: Save the absolute original fill for the Reset function
    if (!el.dataset.originalFill) {
      const currentFill = el.getAttribute("fill") || window.getComputedStyle(el).fill || "none";
      el.dataset.originalFill = currentFill;
    }

    el.style.cursor = "pointer";

    if (colors.value[el.id]) {
      el.setAttribute("fill", colors.value[el.id]);
      el.style.fill = colors.value[el.id]; // Ensure inline styles are also overridden
    }

    el.onclick = (e) => {
      e.stopPropagation(); 
      
      const prevColor = el.getAttribute("fill") || window.getComputedStyle(el).fill;
      const newColor = selectedColor.value;

      // Don't log an undo action if the color didn't actually change
      if (prevColor === newColor) return;

      el.setAttribute("fill", newColor);
      el.style.fill = newColor; 
      colors.value[el.id] = newColor;

      usedColors.value = usedColors.value.filter(c => c !== newColor);
      usedColors.value.unshift(newColor); // Add to the start of the array
      
      // Enforce the limit
      if (usedColors.value.length > MAX_HISTORY) {
        usedColors.value.pop();
      }

      undoStack.value.push({ id: el.id, prevColor, newColor });
      redoStack.value = [];
    };

    el.onmouseenter = () => {
      el.dataset.originalOpacity = el.getAttribute("opacity") || "1";
      el.setAttribute("opacity", "0.7");
    };
    el.onmouseleave = () => {
      el.setAttribute("opacity", el.dataset.originalOpacity);
    };
  });
};

// Undo last change
const undo = () => {
  if (undoStack.value.length === 0) return; // Nothing to undo
  
  const action = undoStack.value.pop();
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  // Safer selector
  const el = svg.querySelector(`[id="${action.id}"]`);
  if (!el) return;

  el.setAttribute("fill", action.prevColor);
  el.style.fill = action.prevColor;
  colors.value[action.id] = action.prevColor;

  redoStack.value.push(action);
};

// Redo last undone change
const redo = () => {
  if (redoStack.value.length === 0) return; // Nothing to redo
  
  const action = redoStack.value.pop();
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const el = svg.querySelector(`[id="${action.id}"]`);
  if (!el) return;

  el.setAttribute("fill", action.newColor);
  el.style.fill = action.newColor;
  colors.value[action.id] = action.newColor;

  undoStack.value.push(action);
};

// Reset everything to original state
const reset = () => {
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const elements = getColorableElements(svg);

  elements.forEach(el => {
    const origFill = el.dataset.originalFill;
    if (origFill) {
      el.setAttribute("fill", origFill);
      el.style.fill = origFill; // Clear any inline styles we injected
    }
  });

  // Wipe the tracking state clean
  colors.value = {};
  undoStack.value = [];
  redoStack.value = [];
};

watch(svgContent, async () => {
  if (svgContent.value) {
    await setupSvgInteractions();
  }
});

onMounted(fetchDesign);
</script>

<style scoped>
.svg-large svg {
  width: 100%;
  max-width: 500px;
}

/* New styles for the recent color swatches */
.gap-2 {
  gap: 8px;
}

.color-swatch {
  width: 32px;
  height: 32px;
  border-radius: 50%; /* Makes them circles */
  cursor: pointer;
  border: 2px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.color-swatch:hover {
  transform: scale(1.15);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Ensure the injected SVG fills the space */
/* :deep(.svg-large svg) {
  width: 100% !important;
  height: auto !important;
  display: block;
  max-height: 500px;
} */

/* Add this to help debug if the SVG is empty */
.svg-large {
  min-height: 200px;
  border: 1px dashed rgba(0,0,0,0.05);
  width: 100%;
}
</style>