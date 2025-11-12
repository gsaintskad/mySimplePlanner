// file: frontend/store/authApi.ts
import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { setCredentials, logOut } from "./authSlice";
import type { RootState } from "./store";

// Define the response type for login
interface LoginResponse {
  success: boolean;
  message: string;
  token?: string;
}

// Define the response type for registration
interface RegisterResponse {
  success: boolean;
  message: string;
}

export const authApi = createApi({
  reducerPath: "authApi",
  baseQuery: fetchBaseQuery({
    baseUrl: "/", // Base URL is root because we are using the proxy
    prepareHeaders: (headers, { getState }) => {
      // Add the auth token to headers if it exists
      const token = (getState() as RootState).auth.token;
      if (token) {
        headers.set("Authorization", `Bearer ${token}`);
      }
      return headers;
    },
  }),
  endpoints: (builder) => ({
    login: builder.mutation<LoginResponse, any>({
      query: (credentials) => ({
        url: "/api/login", // Will be proxied to http://localhost:8080/api/login
        method: "POST",
        body: credentials,
      }),
      async onQueryStarted(arg, { dispatch, queryFulfilled }) {
        try {
          const { data } = await queryFulfilled;
          if (data.token) {
            dispatch(setCredentials({ token: data.token, user: arg.name }));
          }
        } catch (error) {
          // Handle error
        }
      },
    }),
    register: builder.mutation<RegisterResponse, any>({
      query: (credentials) => ({
        url: "/api/register", // Will be proxied to http://localhost:8080/api/register
        method: "POST",
        body: credentials,
      }),
    }),
    // You can add protected endpoints here, e.g., getTasks
  }),
});

// Export hooks for use in components
export const { useLoginMutation, useRegisterMutation } = authApi;
