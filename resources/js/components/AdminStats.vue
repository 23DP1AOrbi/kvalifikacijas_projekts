<template>
  <div class="mt-6">
    <h2 class="text-h5 mb-4">Sistēmas Statistika</h2>
    
    <v-row>
      <v-col cols="12" sm="4">
        <v-card color="primary" variant="tonal" class="text-center pa-4">
          <div class="text-h4 font-weight-bold">{{ stats.total_designs }}</div>
          <div class="text-caption">Dizaini kopā</div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4">
        <v-card color="secondary" variant="tonal" class="text-center pa-4">
          <div class="text-h4 font-weight-bold">{{ stats.total_categories }}</div>
          <div class="text-caption">Kategorijas kopā</div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4">
        <v-card color="info" variant="tonal" class="text-center pa-4">
          <div class="text-h4 font-weight-bold">{{ stats.avg_categories }}</div>
          <div class="text-caption">Vidēji kat. / dizainam</div>
        </v-card>
      </v-col>
    </v-row>

    <v-card class="mt-6">
      <v-card-title>Populārākie dizaini (Saglabāti projektos)</v-card-title>
      <v-table>
        <thead>
          <tr>
            <th class="text-left">Dizaina nosaukums</th>
            <th class="text-center">Reizes saglabāts</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats.top_designs" :key="item.name">
            <td>{{ item.name }}</td>
            <td class="text-center">
              <v-chip color="primary" size="small">{{ item.projects_count }}</v-chip>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../bootstrap.js';

const stats = ref({
  total_designs: 0,
  total_categories: 0,
  avg_categories: 0,
  top_designs: []
});

onMounted(async () => {
  try {
    const res = await axios.get('/api/stats');
    stats.value = res.data;
  } catch (err) {
    console.error("Kļūda ielādējot statistiku", err);
  }
});
</script>