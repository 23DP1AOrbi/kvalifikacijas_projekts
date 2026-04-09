<template>
  <v-container fluid style="max-width: 1600px;">
    <v-btn @click="$router.back()" prepend-icon="mdi-arrow-left" variant="text" class="mb-4">Atpakaļ</v-btn>

    <v-card v-if="svgContent" elevation="2">
      <v-card-title class="pa-4">
        {{ route.query.project ? 'Rediģēt projektu: ' : '' }}{{ design?.name }}
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="3">
            <DesignCategoryManager 
              v-if="user?.role === 'admin'" 
              :design-id="design.id" 
              :initial-categories="design.categories" 
              class="mb-4"
            />

            <v-color-picker
              v-model="selectedColor"
              hide-inputs
              flat
              show-swatches
              class="mb-4"
              width="100%"
            ></v-color-picker>

            <div v-if="usedColors.length > 0" class="mb-4">
              <div class="text-subtitle-2 mb-2 text-grey-darken-1">Pēdējās krāsas</div>
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

            <div class="d-flex justify-space-between align-center mb-6">
              <div>
                <v-btn 
                  icon="mdi-undo" 
                  @click="undo" 
                  class="mr-2" 
                  :disabled="undoStack.length === 0"
                  variant="tonal"
                  density="comfortable"
                ></v-btn>
                <v-btn 
                  icon="mdi-redo" 
                  @click="redo" 
                  :disabled="redoStack.length === 0"
                  variant="tonal"
                  density="comfortable"
                ></v-btn>
              </div>
              
              <v-btn 
                icon="mdi-refresh" 
                @click="reset" 
                color="error" 
                variant="tonal"
                density="comfortable"
              ></v-btn>
            </div>

            <v-divider class="my-4"></v-divider>

            <v-btn 
              block 
              color="primary" 
              prepend-icon="mdi(content-save)" 
              @click="saveProject"
              :loading="isSaving"
              :disabled="!user"
              size="large"
            >
              Saglabāt kā projektu
            </v-btn>
            <div v-if="!user" class="text-caption text-center mt-2 text-grey">
              Lūdzu, piesakieties, lai saglabātu
            </div>
          </v-col>

          <v-col cols="12" md="9" class="d-flex justify-center align-center">
            <div ref="svgContainer" v-html="svgContent" class="svg-display-container"></div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div v-else class="text-center pa-10">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <v-dialog v-model="saveDialog" max-width="400">
      <v-card title="Saglabāt projektu">
        <v-card-text>
          <v-text-field
            v-model="projectName"
            label="Projekta nosaukums"
            variant="outlined"
            hide-details
            @keyup.enter="confirmSave"
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="saveDialog = false">Atcelt</v-btn>
          <v-btn 
            color="primary" 
            variant="elevated" 
            @click="confirmSave" 
            :loading="isSaving"
            >
            Saglabāt
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "../bootstrap.js";
import DesignCategoryManager from './DesignCategoryManager.vue';
import { user } from "../services/auth";

const router = useRouter();
const route = useRoute();
const design = ref(null);
const svgContent = ref('');
const svgContainer = ref(null);
const selectedColor = ref('#ff0000');
const usedColors = ref([]);
const MAX_HISTORY = 12;
const colors = ref({});
const undoStack = ref([]);
const redoStack = ref([]);


const applySavedColors = () => {
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg || !colors.value) return;

  Object.entries(colors.value).forEach(([id, color]) => {
    const el = svg.getElementById(id) || svg.querySelector(`[id="${id}"]`);
    if (el) {
      el.setAttribute("fill", color);
      el.style.fill = color;
    }
  });
};

const fetchDesign = async () => {
  try {
    const res = await axios.get(`/api/dizaini/${route.params.id}`);
    design.value = res.data;

    const svgRes = await axios.get(design.value.file_url, { responseType: 'text' });
    let rawSvg = svgRes.data;

    const svgStartIndex = rawSvg.indexOf('<svg');
    if (svgStartIndex !== -1) {
      rawSvg = rawSvg.substring(svgStartIndex);
    }
    svgContent.value = rawSvg;

    await nextTick();
    await new Promise(resolve => setTimeout(resolve, 100));
    // --- NEW: Check if we are opening a specific saved project ---
    if (route.query.project) {
      fetchProjectData(route.query.project);
    }
  } catch (err) {
    console.error("Fetch Error:", err);
  }
};

const fetchProjectData = async (projectId) => {
  try {
    const res = await axios.get(`/api/projects/${projectId}`);
    let colorData = res.data.color_data;

    if (typeof colorData === 'string') {
      try {
        colorData = JSON.parse(colorData);
      } catch (e) {
        console.error("Invalid JSON in color_data", e);
      }
    }

    colors.value = colorData || {};
    
    // Apply colors to the SVG
    applySavedColors();
  } catch (err) {
    console.error("Failed to load project colors:", err);
  }
};



const getColorableElements = (svg) => {
  return svg.querySelectorAll("path, circle, rect, ellipse, polygon, polyline");
}

const setupSvgInteractions = async () => {
  await nextTick();
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;

  // FIX: Ensure the SVG is responsive and doesn't cut off
  svg.setAttribute('width', '100%');
  svg.setAttribute('height', '100%');
  svg.style.display = 'block';
  svg.style.maxWidth = '100%';
  svg.style.maxHeight = '70vh'; // Prevents it from being taller than the screen

  const elements = getColorableElements(svg);
  elements.forEach((el, index) => {
    if (!el.id) el.id = `svg-shape-${index}`;
    if (!el.dataset.originalFill) {
      el.dataset.originalFill = el.getAttribute("fill") || window.getComputedStyle(el).fill || "none";
    }
    el.style.cursor = "pointer";
    el.style.transition = "fill 0.2s ease, opacity 0.2s ease";

    el.onclick = (e) => {
      e.stopPropagation(); 
      const prevColor = el.getAttribute("fill") || window.getComputedStyle(el).fill;
      const newColor = selectedColor.value;
      if (prevColor === newColor) return;

      el.setAttribute("fill", newColor);
      el.style.fill = newColor; 
      colors.value[el.id] = newColor;

      usedColors.value = [newColor, ...usedColors.value.filter(c => c !== newColor)].slice(0, MAX_HISTORY);
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

    if (Object.keys(colors.value).length > 0) {
      applySavedColors();
    }
  });
};

const undo = () => {
  if (undoStack.value.length === 0) return;
  const action = undoStack.value.pop();
  const svg = svgContainer.value?.querySelector("svg");
  const el = svg?.querySelector(`[id="${action.id}"]`);
  if (!el) return;
  el.setAttribute("fill", action.prevColor);
  el.style.fill = action.prevColor;
  colors.value[action.id] = action.prevColor;
  redoStack.value.push(action);
};

const redo = () => {
  if (redoStack.value.length === 0) return;
  const action = redoStack.value.pop();
  const svg = svgContainer.value?.querySelector("svg");
  const el = svg?.querySelector(`[id="${action.id}"]`);
  if (!el) return;
  el.setAttribute("fill", action.newColor);
  el.style.fill = action.newColor;
  colors.value[action.id] = action.newColor;
  undoStack.value.push(action);
};

const reset = () => {
  if (!confirm("Vai tiešām vēlaties atiestatīt visas krāsas?")) return;
  const svg = svgContainer.value?.querySelector("svg");
  if (!svg) return;
  getColorableElements(svg).forEach(el => {
    const origFill = el.dataset.originalFill;
    if (origFill) {
      el.setAttribute("fill", origFill);
      el.style.fill = origFill;
    }
  });
  colors.value = {};
  undoStack.value = [];
  redoStack.value = [];
};

const saveDialog = ref(false);
const projectName = ref('');
const isSaving = ref(false);

const saveProject = () => {
  // If we have a project ID in the URL, it's an existing project
  if (route.query.project) {
    // Skip the dialog and save immediately
    projectName.value = design.value.name; // Keep existing name logic
    confirmSave();
  } else {
    // If it's a fresh design, ask for a name
    projectName.value = design.value.name;
    saveDialog.value = true;
  }
};

const confirmSave = async () => {
  if (!projectName.value && !route.query.project) return;
 
  isSaving.value = true;
  try {
    const isUpdate = !!route.query.project;
    const url = isUpdate ? `/api/projects/${route.query.project}` : '/api/projects';
    const method = isUpdate ? 'put' : 'post';

    await axios({
      method: method,
      url: url,
      data: {
        design_id: design.value.id,
        name: projectName.value,
        color_data: Object.keys(colors.value).length > 0 ? colors.value : {}
      }
    });

    saveDialog.value = false;
    router.push('/profils');
  } catch (error) {
    // THIS IS THE MOST IMPORTANT PART FOR DEBUGGING:
    if (error.response && error.response.status === 422) {
      console.error("Validation Errors:", error.response.data.errors);
      // This will print exactly which field failed (name, design_id, or color_data)
    } else {
      console.error("Save Error:", error);
    }
  }
};



watch(svgContent, async () => {
  if (svgContent.value) await setupSvgInteractions();
});

onMounted(fetchDesign);
</script>

<style scoped>
.svg-display-container {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: repeating-conic-gradient(#f5f5f5 0% 25%, #fff 0% 50%) 50% / 20px 20px; /* Checkerboard pattern for transparency */
  border: 1px solid rgba(0,0,0,0.05);
  border-radius: 8px;
  padding: 20px;
  min-height: 400px;
}

/* Ensure the SVG scales within the container correctly */
:deep(svg) {
  width: 100%;
  height: auto;
  max-height: 500px;
}

.gap-2 {
  gap: 8px;
}

.color-swatch {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.color-swatch:hover {
  transform: scale(1.15);
}
</style>