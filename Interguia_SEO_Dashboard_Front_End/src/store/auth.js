import {create} from "zustand";
import {persist} from "zustand/midleware"


const useAuthStore = create(
    persist(
        (set) => ({
            currentUser: null,
            token: null,
            isAuthenticated: false,

            //acciones

            login:(currentUser, token) =>
                set({currentUser, token, isAuthenticated: true}),

            logout:()=>
                set({currentUser:null, token:null, isAuthenticated:false}),
        }),
        {
            name: "auth-storage",
        }
    )
);


export default useAuthStore;