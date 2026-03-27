import { ref } from "vue";
import axios from "../bootstrap.js";

// global reactive state
export const user = ref(null);

// fetch current user from Laravel
export const fetchUser = async () => {
  try {
    const res = await axios.get("/user");
    // Only assign if res.data has an id
    user.value = res.data && res.data.id ? res.data : null;
    // console.log("Fetched user:", user.value);
  } catch {
    user.value = null;
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

// logout
export const logout = async () => {
  try {
    await axios.post("/logout");

    // Immediately set user to null
    user.value = null;

    // Optional: fetch again to confirm
    await fetchUser(); // fetchUser() will set user.value = null if logged out
    window.location.href = "/";
  } catch (err) {
    console.error(err);
  }
};
