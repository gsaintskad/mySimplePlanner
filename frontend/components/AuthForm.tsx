// file: frontend/components/AuthForm.tsx
"use client";

import React, { useState, FormEvent } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useLoginMutation, useRegisterMutation } from "../store/authApi";
import { useAppDispatch } from "../store/hooks";

type AuthMode = "login" | "register";

export default function AuthForm({ mode }: { mode: AuthMode }) {
  const [name, setName] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);

  const router = useRouter();
  const dispatch = useAppDispatch();

  const [login, { isLoading: isLoggingIn }] = useLoginMutation();

  const [register, { isLoading: isRegistering }] = useRegisterMutation();

  const isLoading = isLoggingIn || isRegistering;

  const title = mode === "login" ? "Sign In" : "Sign Up";
  const linkText =
    mode === "login" ? "Don't have an account?" : "Already have an account?";
  const linkHref = mode === "login" ? "/register" : "/login";

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);

    try {
      if (mode === "login") {
        const data = await login({ name, password }).unwrap();
        if (data.success) {
          router.push("/dashboard"); // Redirect to a protected page
        } else {
          setError(data.message || "Invalid name or password");
        }
      } else {
        const data = await register({ name, password }).unwrap();
        if (data.success) {
          // Automatically log in after successful registration
          const loginData = await login({ name, password }).unwrap();
          if (loginData.success) {
            router.push("/dashboard");
          } else {
            setError(loginData.message || "Login failed after registration.");
            router.push("/login");
          }
        } else {
          setError(data.message || "Registration failed.");
        }
      }
    } catch (err: any) {
      setError(
        err.data?.message || err.message || "An unexpected error occurred."
      );
    }
  };

  return (
    <div className="flex min-h-screen w-full items-center justify-center bg-neutral-100 dark:bg-black p-4">
      <div className="w-full max-w-md">
        <div className="bg-white dark:bg-neutral-900 shadow-xl rounded-2xl p-8 sm:p-12">
          <h1 className="text-3xl font-semibold text-center text-black dark:text-white mb-8">
            {title}
          </h1>
          <form onSubmit={handleSubmit} className="space-y-6">
            {error && (
              <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-center">
                {error}
              </div>
            )}
            <div>
              <label
                htmlFor="name"
                className="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2"
              >
                Username
              </label>
              <input
                id="name"
                name="name"
                type="text"
                required
                autoComplete="username"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="w-full p-3 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label
                htmlFor="password"
                className="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2"
              >
                Password
              </label>
              <input
                id="password"
                name="password"
                type="password"
                required
                autoComplete="current-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full p-3 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <button
                type="submit"
                disabled={isLoading}
                className="w-full flex justify-center py-3 px-4 rounded-lg font-semibold text-lg bg-black dark:bg-white text-white dark:text-black transition-opacity duration-200 hover:opacity-80 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isLoading ? "Processing..." : title}
              </button>
            </div>
          </form>
        </div>

        <p className="mt-8 text-center text-sm text-neutral-600 dark:text-neutral-400">
          {linkText}{" "}
          <Link
            href={linkHref}
            className="font-medium text-blue-600 hover:text-blue-500"
          >
            {mode === "login" ? "Sign Up" : "Sign In"}
          </Link>
        </p>
      </div>
    </div>
  );
}
