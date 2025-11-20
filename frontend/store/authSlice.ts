// file: frontend/store/authSlice.ts
import { createSlice, PayloadAction } from "@reduxjs/toolkit";

interface AuthState {
  user: string | null;
  token: string | null;
  isAuthenticated: boolean;
}

// 1. Helper to read from LocalStorage on initialization
const getInitialState = (): AuthState => {
  if (typeof window !== "undefined") {
    const token = localStorage.getItem("token");
    const user = localStorage.getItem("user");
    if (token) {
      return {
        token,
        user,
        isAuthenticated: true,
      };
    }
  }
  return {
    user: null,
    token: null,
    isAuthenticated: false,
  };
};

const initialState: AuthState = getInitialState();

const authSlice = createSlice({
  name: "auth",
  initialState,
  reducers: {
    setCredentials: (
      state,
      action: PayloadAction<{ user: string; token: string }>
    ) => {
      state.user = action.payload.user;
      state.token = action.payload.token;
      state.isAuthenticated = true;

      // 2. Save to LocalStorage on login/refresh
      localStorage.setItem("user", action.payload.user);
      localStorage.setItem("token", action.payload.token);
    },
    logOut: (state) => {
      state.user = null;
      state.token = null;
      state.isAuthenticated = false;

      // 3. Clear LocalStorage on logout
      localStorage.removeItem("user");
      localStorage.removeItem("token");
    },
  },
});

export const { setCredentials, logOut } = authSlice.actions;

export default authSlice.reducer;
