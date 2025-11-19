import axios from "axios";

window.axios = axios;

// Define a baseURL pro Axios pegar HTTPS
window.axios.defaults.baseURL =
    import.meta.env.VITE_APP_URL || window.location.origin;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
