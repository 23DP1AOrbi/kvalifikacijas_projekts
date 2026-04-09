<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <h1 class="mb-6">Mans Profils</h1>

        <v-card class="pa-6 mb-6">
          <v-card-title class="px-0 d-flex align-center">
            <span class="text-h5 font-weight-bold">{{ form.name }}</span>
            <v-spacer></v-spacer>
            <v-btn 
              v-if="!isEditing" 
              prepend-icon="mdi-pencil" 
              color="primary" 
              variant="tonal" 
              size="small"
              @click="isEditing = true"
            >
              Labot profilu
            </v-btn>
          </v-card-title>

          <div v-if="!isEditing" class="mt-1 text-body-1 text-medium-emphasis">
            <v-icon start icon="mdi-email" size="small"></v-icon>
            {{ form.email }}
          </div>

          <v-divider v-if="!isEditing" class="my-4"></v-divider>
          
          <v-form v-if="isEditing" @submit.prevent="updateProfile" class="mt-4">
            <v-text-field
              v-model="form.name"
              label="Lietotājvārds"
              :error-messages="errors.name"
              variant="outlined"
              density="comfortable"
            />

            <v-text-field
              v-model="form.email"
              label="E-pasts"
              type="email"
              :error-messages="errors.email"
              variant="outlined"
              density="comfortable"
            />

            <v-expansion-panels variant="accordion" class="mb-4">
              <v-expansion-panel title="Mainīt paroli">
                <v-expansion-panel-text>
                  <v-text-field
                    v-model="form.password"
                    label="Jaunā parole"
                    type="password"
                    :error-messages="errors.password"
                    variant="outlined"
                  />
                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Apstiprināt jauno paroli"
                    type="password"
                    variant="outlined"
                  />
                </v-expansion-panel-text>
              </v-expansion-panel>
            </v-expansion-panels>

            <div class="d-flex gap-4">
              <v-btn 
                type="submit" 
                color="success" 
                :loading="loading" 
                class="flex-grow-1"
              >
                Saglabāt
              </v-btn>
              <v-btn 
                variant="outlined" 
                color="secondary" 
                @click="cancelEdit"
              >
                Atcelt
              </v-btn>
            </div>
          </v-form>

          <v-alert v-if="successMessage" type="success" variant="tonal" class="mt-4" closable>
            {{ successMessage }}
          </v-alert>
        </v-card>

        <v-card class="pa-6">
          <div class="d-flex gap-4 mb-6">
            <v-btn variant="tonal" prepend-icon="mdi-folder-star" color="indigo">Mani Projekti</v-btn>
            <v-btn variant="text" prepend-icon="mdi-heart" color="error">Favorīti</v-btn>
            <v-btn variant="text" prepend-icon="mdi-history">Nesenie</v-btn>
          </div>

          <v-divider class="mb-6"></v-divider>

          <v-row v-if="projects.length > 0">
            <v-col v-for="project in projects" :key="project.id" cols="12" sm="6" md="4">
              <v-card 
                variant="outlined" 
                class="project-card clickable-card"
                @click="router.push(`/dizaini/${project.design_id}?project=${project.id}`)"
              >
                <div class="delete-action">
                  <v-btn 
                    icon="mdi-delete" 
                    size="x-small" 
                    color="error" 
                    variant="flat"
                    @click.stop="deleteProject(project.id)"
                  ></v-btn>
                </div>

                <div class="project-preview pa-2">
                  <div 
                    v-if="project.rendered_svg" 
                    v-html="project.rendered_svg" 
                    class="svg-wrapper"
                  ></div>
                  <v-progress-circular v-else indeterminate size="20"></v-progress-circular>
                  
                  <div class="card-overlay">
                    <v-icon icon="mdi-eye" color="white" size="large"></v-icon>
                  </div>
                </div>
                
                <v-card-item>
                  <div class="project-title truncate">{{ project.name }}</div>
                  <div class="project-date">
                    {{ new Date(project.created_at).toLocaleDateString('lv-LV') }}
                  </div>
                </v-card-item>
              </v-card>
            </v-col>
          </v-row>

          <div v-else-if="!projectsLoading" class="text-center py-10">
            <v-icon icon="mdi-folder-open-outline" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
            <div class="text-h6 text-grey-darken-1">Jums vēl nav saglabātu projektu</div>
            <v-btn color="primary" class="mt-4" to="/dizaini">Apskatīt dizainus</v-btn>
          </div>

          <v-skeleton-loader v-else type="card, card, card" />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "../bootstrap.js";
import { useRouter } from "vue-router"

const router = useRouter();
const isEditing = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errors = ref({});

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const originalData = ref({});

const fetchUserData = async () => {
  try {
    await axios.get("/sanctum/csrf-cookie");

    const res = await axios.get("/api/user");
    form.value.name = res.data.name;
    form.value.email = res.data.email;
    originalData.value = { ...res.data };
  } catch (err) {
    if (err.response?.status === 401) {
      console.error("Lietotājs nav autorizējies");
      // Optional: window.location.href = "/login";
    } else {
      console.error("Neizdevās ielādēt datus", err);
    }
  }
};

const cancelEdit = () => {
  // Revert form values to original database state
  form.value.name = originalData.value.name;
  form.value.email = originalData.value.email;
  form.value.password = "";
  form.value.password_confirmation = "";
  errors.value = {};
  isEditing.value = false;
};

const updateProfile = async () => {
  errors.value = {};
  successMessage.value = "";
  loading.value = true;

  try {
    await axios.get("/sanctum/csrf-cookie");

    const res = await axios.put("/api/user/update", form.value);
    successMessage.value = "Profils veiksmīgi atjaunināts!";
    
    // Update originalData with the new saved info
    originalData.value = { 
      name: form.value.name, 
      email: form.value.email 
    };
    
    form.value.password = "";
    form.value.password_confirmation = "";
    isEditing.value = false;
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error(error);
    }
  } finally {
    loading.value = false;
  }
};

const projects = ref([]);
const projectsLoading = ref(false);

const fetchProjects = async () => {
  projectsLoading.value = true;
  try {
    const res = await axios.get("/api/projects");
    // We create a local copy first
    const projectsData = res.data;

    // Use Promise.all to wait for all previews to finish before assigning to the ref
    await Promise.all(projectsData.map(project => renderProjectPreview(project)));
    
    // Assign the fully populated array all at once
    projects.value = projectsData;
  } catch (err) {
    console.error("Neizdevās ielādēt projektus", err);
  } finally {
    projectsLoading.value = false;
  }
};

const renderProjectPreview = async (project) => {
  try {
    const designRes = await axios.get(`/api/dizaini/${project.design_id}`);
    const svgRes = await axios.get(designRes.data.file_url, { responseType: 'text' });
    
    const parser = new DOMParser();
    const doc = parser.parseFromString(svgRes.data, "image/svg+xml");
    const svgEl = doc.documentElement;

    if (!svgEl || svgEl.nodeName !== "svg") return;

    // 1. Standardize ViewBox
    const w = svgEl.getAttribute('width');
    const h = svgEl.getAttribute('height');
    if (!svgEl.getAttribute('viewBox') && w && h) {
      svgEl.setAttribute('viewBox', `0 0 ${w.replace('px', '')} ${h.replace('px', '')}`);
    }
    svgEl.removeAttribute('width');
    svgEl.removeAttribute('height');

    // 2. IMPORTANT: Manually assign IDs to shapes based on index
    // This replicates how your editor likely generates "svg-shape-N"
    const shapes = svgEl.querySelectorAll('path, rect, circle, polygon, ellipse, text');
    shapes.forEach((shape, index) => {
      shape.setAttribute('id', `svg-shape-${index}`);
    });

    // 3. Parse color_data
    let colors = project.color_data;
    if (typeof colors === 'string') {
      try { colors = JSON.parse(colors); } catch (e) { colors = null; }
    }

    // 4. Inject saved colors
    if (colors && typeof colors === 'object') {
      Object.entries(colors).forEach(([id, color]) => {
        const el = svgEl.getElementById(id);
                   
        if (el) {
          el.setAttribute("fill", color);
          el.style.fill = color;
          
          // Handle strokes for lines/outlines
          if (el.getAttribute('stroke') && el.getAttribute('stroke') !== 'none') {
             el.setAttribute("stroke", color);
             el.style.stroke = color;
          }
          
          el.style.opacity = "1"; 
        }
      });
    }

    project.rendered_svg = new XMLSerializer().serializeToString(svgEl);
  } catch (err) {
    console.error("Preview render failed:", err);
    project.rendered_svg = '<span class="text-caption">Preview unavailable</span>';
  }
};

const deleteProject = async (id) => {
  if (!confirm("Vai tiešām vēlaties dzēst šo projektu?")) return;
  try {
    await axios.delete(`/api/projects/${id}`);
    projects.value = projects.value.filter(p => p.id !== id);
  } catch (err) {
    console.error("Dzēšana neizdevās", err);
  }
};

onMounted(() => {
  fetchUserData();
  fetchProjects();
});

</script>

<style scoped>
.gap-4 { display: flex; gap: 16px; }

.project-card {
  overflow: hidden;
  border-radius: 8px;
  background: white;
  border: 1px solid rgba(0,0,0,0.08);
  /* Force GPU acceleration to stop the lag */
  transform: translateZ(0);
  backface-visibility: hidden;
}

.clickable-card {
  cursor: pointer;
  position: relative;
}

.clickable-card:hover {
  box-shadow: 0 6px 16px rgba(0,0,0,0.1) !important;
}

.delete-action {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 10;
  opacity: 0;
  transition: opacity 0.15s ease-in-out;
  cursor: pointer;
}

.clickable-card:hover .delete-action {
  opacity: 1;
}

.delete-action,
.delete-action .v-btn {
  cursor: pointer !important;
}

.delete-action .v-btn {
  pointer-events: auto; 
}

.project-preview {
  height: 180px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #ffffff;
  background-image: linear-gradient(45deg, #fafafa 25%, transparent 25%), 
                    linear-gradient(-45deg, #fafafa 25%, transparent 25%), 
                    linear-gradient(45deg, transparent 75%, #fafafa 75%), 
                    linear-gradient(-45deg, transparent 75%, #fafafa 75%);
  background-size: 16px 16px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.card-overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s ease;
  pointer-events: none;
}

.clickable-card:hover .card-overlay {
  opacity: 1;
}

.svg-wrapper {
  width: 100%;
  height: 100%;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none; 
}

:deep(.svg-wrapper svg) {
  width: auto !important;
  height: auto !important;
  max-width: 100%;
  max-height: 100%;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
  shape-rendering: geometricPrecision;
}

.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.project-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1a1a1a;
}

.project-date {
  font-size: 0.75rem;
  color: #9e9e9e;
}

.v-card-item {
  padding: 10px 12px 4px 12px !important;
}
</style>