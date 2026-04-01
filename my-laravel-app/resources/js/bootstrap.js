import axios from "axios";

window.axios = axios;

axios.defaults.baseURL = "http://localhost:8000"; // Laravel origin
axios.defaults.withXSRFToken = true;
axios.defaults.withCredentials = true; // include session cookies
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common['Accept'] = 'application/json';

export default axios;
