<template>
  <v-card class="pa-6">
  <div class="d-flex align-center gap-4 mb-6">
    <v-btn 
      :variant="currentFilter === 'all' ? 'tonal' : 'text'" 
      prepend-icon="mdi-folder-star" 
      color="indigo"
      @click="setFilter('all')"
    >
      Mani Projekti
    </v-btn>
    <!-- later -->
    <!-- <v-btn variant="text" prepend-icon="mdi-heart" color="error">Favorīti</v-btn> -->
    
    <v-btn 
      :variant="currentFilter === 'recent' ? 'tonal' : 'text'" 
      prepend-icon="mdi-history"
      @click="setFilter('recent')"
    >
      Nesenie
    </v-btn>

    <v-divider vertical class="mx-2"></v-divider>

    <v-btn
        variant="outlined"
        size="small"
        :icon="sortOrder === 'desc' ? 'mdi-sort-calendar-descending' : 'mdi-sort-calendar-ascending'"
        @click="toggleSort"
      >
        <v-icon :icon="sortOrder === 'desc' ? 'mdi-arrow-down' : 'mdi-arrow-up'"></v-icon>
      </v-btn>
  </div>

  <v-divider class="mb-6"></v-divider>

  <v-row v-if="filteredProjects.length > 0">
    <v-col v-for="project in filteredProjects" :key="project.id" cols="12" sm="6" md="6">
              <v-card 
                variant="outlined" 
                class="project-card clickable-card"
                @click="router.push(`/dizaini/${project.design_id}?project=${project.id}`)"
              >
                <div class="rename-action">
                  <v-btn 
                    icon="mdi-pencil" 
                    size="x-small" 
                    color="primary" 
                    variant="flat"
                    @click.stop="openRenameDialog(project)"
                  ></v-btn>
                </div>

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
                    <v-icon 
                      icon="mdi-eye" 
                      color="white" 
                      size="large" 
                    ></v-icon>
                  </div>
                </div>
                
                <v-card-item>
                  <div class="project-title truncate">{{ project.name }}</div>
                  <div class="project-date">
                    Rediģēts:
                    {{ new Date(project.updated_at).toLocaleString('lv-LV', { dateStyle: 'short', timeStyle: 'short' }) }}
                  </div>
                </v-card-item>
              </v-card>
            </v-col>
          </v-row>

          <v-row v-else-if="projectsLoading">
            <v-col v-for="n in 3" :key="n" cols="12" sm="6" md="4">
              <v-skeleton-loader type="card" />
            </v-col>
          </v-row>

          <div v-else class="text-center py-10">
            <v-icon icon="mdi-folder-open-outline" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
            <div class="text-h6 text-grey-darken-1">Jums vēl nav saglabātu projektu</div>
            <v-btn color="primary" class="mt-4" to="/dizaini">Apskatīt dizainus</v-btn>
          </div>

          <v-dialog v-model="renameDialog" max-width="400">
            <v-card title="Pārsaukt projektu">
              <v-card-text>
                <v-text-field
                  v-model="newName"
                  label="Jaunais nosaukums"
                  variant="outlined"
                  hide-details
                  @keyup.enter="confirmRename"
                ></v-text-field>
              </v-card-text>
              <v-card-actions class="pa-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="renameDialog = false">Atcelt</v-btn>
                <v-btn color="primary" variant="elevated" @click="confirmRename" :loading="renameLoading">Saglabāt</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "../bootstrap.js";

const router = useRouter();
const projects = ref([]);
const projectsLoading = ref(false);

const fetchProjects = async () => {
  projectsLoading.value = true;
  try {
    const res = await axios.get("/api/projects");

    const projectsData = res.data;

    // waits for all the previews to load
    await Promise.all(projectsData.map(project => renderProjectPreview(project)));
    
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

    // adds ids to all shapes in the vector
    const shapes = svgEl.querySelectorAll('path, rect, circle, polygon, ellipse, text');
    shapes.forEach((shape, index) => {
      shape.setAttribute('id', `svg-shape-${index}`);
    });

    // changes color_data to json
    let colors = project.color_data;
    if (typeof colors === 'string') {
      try { colors = JSON.parse(colors); } catch (e) { colors = null; }
    }

    // inserts the changed colors into the original designs
    if (colors && typeof colors === 'object') {
      Object.entries(colors).forEach(([id, color]) => {
        const el = svgEl.getElementById(id);
                   
        if (el) {
          el.setAttribute("fill", color);
          el.style.fill = color;
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

const renameDialog = ref(false);
const renameLoading = ref(false);
const newName = ref('');
const selectedProject = ref(null);

const openRenameDialog = (project) => {
  selectedProject.value = project;
  newName.value = project.name; 
  renameDialog.value = true;
};

const confirmRename = async () => {
  if (!newName.value.trim() || !selectedProject.value) {
    alert("Kļūda: Nosaukums nevar būt tukšs!");
    return;
  } 
  renameLoading.value = true;
  
  try {
    await axios.put(`/api/projects/${selectedProject.value.id}`, {
      design_id: selectedProject.value.design_id,
      name: newName.value,
      color_data: selectedProject.value.color_data 
    });
    
    // tells which project name to update by id
    const index = projects.value.findIndex(p => p.id === selectedProject.value.id);
    if (index !== -1) {
      projects.value[index].name = newName.value;
      projects.value[index].updated_at = new Date().toISOString();
    }
    
    renameDialog.value = false;
  } catch (err) {
    console.error("Pārsaukšana neizdevās", err);
    alert("Kļūda: Neizdevās pārsaukt projektu.");
  } finally {
    renameLoading.value = false;
  }
};

const currentFilter = ref('all'); 
const sortOrder = ref('desc');

const filteredProjects = computed(() => {
  let list = [...projects.value];

  if (currentFilter.value === 'recent') {
    // nesenie - sorts by creation date
    list.sort((a, b) => {
      const dateA = new Date(a.created_at);
      const dateB = new Date(b.created_at);
      return sortOrder.value === 'desc' ? dateB - dateA : dateA - dateB;
    });
  } else {
    // mani projekti - sorts by updated date & time
    list.sort((a, b) => {
      const dateA = new Date(a.updated_at);
      const dateB = new Date(b.updated_at);
      return sortOrder.value === 'desc' ? dateB - dateA : dateA - dateB;
    });
  }

  return list;
});

const toggleSort = () => {
  sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
};

const setFilter = (filter) => {
  currentFilter.value = filter;
};

onMounted(fetchProjects);
</script>

<style scoped>
.gap-4 { 
  display: flex;
  gap: 16px; 
 }

.project-card {
  overflow: hidden;
  border-radius: 8px;
  background: white;
  border: 1px solid rgba(0,0,0,0.08);
  backface-visibility: hidden;
}

.clickable-card {
  cursor: pointer;
  position: relative;
}

.clickable-card:hover {
  box-shadow: 0 6px 16px rgba(0,0,0,0.1) !important;
}

.rename-action {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 10;
  opacity: 0;
  transition: opacity 0.15s ease-in-out;
}

.delete-action {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 10;
  opacity: 0;
  transition: opacity 0.15s ease-in-out;
}

.clickable-card:hover .rename-action,
.clickable-card:hover .delete-action {
  opacity: 1;
}

.rename-action .v-btn,
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