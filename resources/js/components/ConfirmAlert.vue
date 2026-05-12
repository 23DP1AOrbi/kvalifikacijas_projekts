<template>
  <v-dialog v-model="show" max-width="450" persistent>
    <v-card class="pa-4 rounded-lg">
      <v-card-item>
        <template v-slot:prepend>
          <v-icon color="error" icon="mdi-alert-circle-outline" size="large"></v-icon>
        </template>
        <v-card-title class="text-h6">Apstiprināt dzēšanu</v-card-title>
      </v-card-item>

      <v-card-text class="py-4">
        Vai tiešām vēlaties dzēst <strong>{{ itemName }}</strong>? 
        Šī darbība ir neatgriezeniska.
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn 
          variant="text" 
          color="grey-darken-1" 
          @click="close"
          class="px-4"
        >
          Atcelt
        </v-btn>
        <v-btn 
          color="error" 
          variant="flat" 
          @click="confirm"
          class="px-6"
        >
          Dzēst
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  itemName: String
});

const emit = defineEmits(['confirm']);
const show = ref(false);

const open = () => {
  show.value = true;
};

const close = () => {
  show.value = false;
};

const confirm = () => {
  emit('confirm');
  close();
};

defineExpose({ open, close });
</script>