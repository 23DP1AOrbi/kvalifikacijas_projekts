import { ref } from "vue";
import axios from "../bootstrap.js";


export const user = ref(null);

// fetch current user from laravel
export const fetchUser = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');

    const res = await axios.get("/api/user");
    // Only assign if res.data has an id
    user.value = res.data && res.data.id ? res.data : null;
  } catch (err) {
    
    user.value = null;
    
    if (err.response?.status === 401) {
        user.value = null;
        return null;
    }
    console.error(err);
  }
};

export const register = async (form) => {
  await axios.get("/sanctum/csrf-cookie");   
  await axios.post("/register", form);       
  await fetchUser();                        
};
// login
export const login = async (form) => {
  await axios.get("/sanctum/csrf-cookie");
  const res = await axios.post("/login", form);
  await fetchUser();
  return res;
};

export const logout = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    await axios.post("/api/logout");

    user.value = null;

    window.location.href = "/";
  } catch (err) {
    console.error(err);
  }
};
