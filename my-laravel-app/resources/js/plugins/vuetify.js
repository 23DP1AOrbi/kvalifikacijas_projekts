// resources/js/plugins/vuetify.js
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
 theme: {
    defaultTheme: 'dark',
    themes: {
      light: {
        dark: false,
        colors: {
          background: '#F1F5F9', 
          surface: '#FFFFFF',    
          primary: '#0F172A',    
          secondary: '#475569',  
          accent: '#2563EB',     
          error: '#EF4444',
          info: '#3B82F6',
          success: '#22C55E',
          warning: '#F59E0B',
        },
        variables: {
          'border-color': '#CBD5E1',
          'hover-opacity': 0.04,
          'focus-opacity': 0.10,
          'selected-opacity': 0.08,
          'activated-opacity': 0.12,
          'pressed-opacity': 0.12,
          'dragged-opacity': 0.08,
        },
      },
      dark: {
        dark: true,
        colors: {
          background: '#0F172A',
          surface: '#1E293B',
          primary: '#3B82F6',
          secondary: '#94A3B8',
          accent: '#60A5FA',
        },
      },
    },
  },
})

export default vuetify