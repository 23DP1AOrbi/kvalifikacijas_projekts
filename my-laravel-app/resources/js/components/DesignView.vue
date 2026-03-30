<template>
  <v-container>
    <v-btn @click="$router.back()" class="mb-4">Back</v-btn>

    <v-card v-if="svgContent">
      <v-card-title>{{ design?.name }}</v-card-title>

      <!-- Color picker -->
      <v-color-picker
        v-model="selectedColor"
        hide-inputs
        flat
        class="mb-4"
      ></v-color-picker>

      <v-btn @click="undo" class="mr-2">Undo</v-btn>
      <v-btn @click="redo">Redo</v-btn>

      <v-card-text>
        <!-- SVG injected -->
        <div ref="svgContainer" v-html="svgContent" class="svg-large"></div>
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

    const svgRes = await axios.get(design.value.file_url);
    svgContent.value = svgRes.data;
  } catch (err) {
    console.error(err);
  }
};

const getColorableElements = (svg) => {
  // Query for all shapes that can typically be colored
  return svg.querySelectorAll("path, circle, rect, ellipse, polygon, polyline");
};
// // Recursively find all paths/groups with id
// const getColorableElements = (el) => {
//   const elements = [];
//   if (el.id) elements.push(el);

//   el.childNodes.forEach(child => {
//     if (child.nodeType === 1) { // element
//       elements.push(...getColorableElements(child));
//     }
//   });

//   return elements;
// };

// Setup SVG interactions
// const setupSvgInteractions = async () => {
//   await nextTick();
//   const svg = svgContainer.value?.querySelector("svg");
//   if (!svg) return;

//   const elements = getColorableElements(svg);

//   elements.forEach(el => {
//     el.style.cursor = "pointer";

//     // Apply saved color
//     if (colors.value[el.id]) {
//       el.setAttribute("fill", colors.value[el.id]);
//     }

//     // Hover highlight
//     el.addEventListener("mouseenter", () => {
//       el.dataset.originalStroke = el.getAttribute("stroke") || "";
//       el.setAttribute("stroke", "#000");
//       el.setAttribute("stroke-width", "1");
//       el.setAttribute("opacity", "0.8")
//     });

//     el.addEventListener("mouseleave", () => {
//       el.setAttribute("stroke", el.dataset.originalStroke);
//       el.setAttribute("opacity",el.dataset.originalOpacity);
//       el.removeAttribute("stroke-width");
//     });

//     // Click to change color
//     el.addEventListener("click", () => {
//       const prevColor = el.getAttribute("fill") || "#000000";
//       const newColor = selectedColor.value;

//       el.setAttribute("fill", newColor);
//       colors.value[el.id] = newColor;

//       // Push to undo stack
//       undoStack.value.push({ id: el.id, prevColor, newColor });
//       redoStack.value = []; // clear redo on new action
//     });
//   });
// };
const setupSvgInteractions = async () => {
  await nextTick();
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const elements = getColorableElements(svg);

  elements.forEach((el, index) => {
    // 1. Ensure element has a unique ID for state tracking
    if (!el.id) {
      el.id = `svg-shape-${index}`;
    }

    el.style.cursor = "pointer";

    // 2. Apply saved color if exists
    if (colors.value[el.id]) {
      el.setAttribute("fill", colors.value[el.id]);
    }

    // 3. Click handler
    el.onclick = (e) => {
      e.stopPropagation(); // Prevent bubbling to parent groups
      
      // Get current color (check computed style if attribute is missing)
      const prevColor = el.getAttribute("fill") || window.getComputedStyle(el).fill;
      const newColor = selectedColor.value;

      el.setAttribute("fill", newColor);
      el.style.fill = newColor; // Force style override
      
      colors.value[el.id] = newColor;

      undoStack.value.push({ id: el.id, prevColor, newColor });
      redoStack.value = [];
    };

    // 4. Hover Effects
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
  const action = undoStack.value.pop();
  if (!action) return;

  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const el = svg.querySelector(`#${action.id}`);
  if (!el) return;

  el.setAttribute("fill", action.prevColor);
  colors.value[action.id] = action.prevColor;

  redoStack.value.push(action);
};

// Redo last undone change
const redo = () => {
  const action = redoStack.value.pop();
  if (!action) return;

  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  const el = svg.querySelector(`#${action.id}`);
  if (!el) return;

  el.setAttribute("fill", action.newColor);
  colors.value[action.id] = action.newColor;

  undoStack.value.push(action);
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
</style>