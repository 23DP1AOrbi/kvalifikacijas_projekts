import axios from "axios";

window.axios = axios;

axios.defaults.baseURL = window.location.origin;
axios.defaults.withXSRFToken = true;
axios.defaults.withCredentials = true; 
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common['Accept'] = 'application/json';

export default axios;
