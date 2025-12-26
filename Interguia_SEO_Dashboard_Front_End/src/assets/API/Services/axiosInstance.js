import axios from "axios";

import useAuthStore from "../../../store/auth";


const api = axios.create({
    baseURL: "local",
    headers: {"Content-Type": "application/json"},
});

//Si el token existe
api.interceptors.request.use((config)=>{
    const {token} = useAuthStore.getState();

    if(token){
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
})

//ASYNC/AWAIT wrapper funciones

//GET
export const apiGet = async (url, config = {}) => {
  const response = await api.get(url, config);
  return response.data;
};

//POST
export const apiPost = async (url, data = {}, config = {}) => {
  const response = await api.post(url, data, config);
  return response.data;
};

//PUT
export const apiPut = async (url, data = {}, config = {}) => {
  const response = await api.put(url, data, config);
  return response.data;
};

//DELETE
export const apiDelete = async (url, config = {}) => {
  const response = await api.delete(url, config);
  return response.data;
};


//PATCH
export const apiPatch = async (url, data = {}, config = {}) => {
  const response = await api.patch(url, data, config);
  return response.data;
};

export default api;