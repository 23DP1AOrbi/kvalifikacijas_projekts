// import axios from "axios";

// axios.defaults.withCredentials = true;
// axios.defaults.baseURL = 'http://localhost:8000';

// const token = document
//   .querySelector('meta[name="csrf-token"]')
//   ?.getAttribute("content");

// if (token) {
//   axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
// }

// export default axios;

import axios from "axios";

window.axios = axios;

axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// DO NOT manually set X-CSRF-TOKEN
// Axios will automatically read the XSRF-TOKEN cookie set by /sanctum/csrf-cookie

export default axios;
