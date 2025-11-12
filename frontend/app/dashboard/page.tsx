// file: frontend/app/dashboard/page.tsx
"use client";

import AuthGuard from "../../components/AuthGuard";
import { useAppSelector, useAppDispatch } from "../../store/hooks";
import { logOut } from "../../store/authSlice";
import { useRouter } from "next/navigation";

export default function DashboardPage() {
  const dispatch = useAppDispatch();
  const router = useRouter();
  const user = useAppSelector((state) => state.auth.user);

  const handleLogout = () => {
    dispatch(logOut());
    router.push("/login");
  };

  return (
    <AuthGuard>
      <div className="flex min-h-screen w-full flex-col items-center justify-center bg-neutral-100 dark:bg-black p-4">
        <div className="w-full max-w-md text-center">
          <h1 className="text-3xl font-semibold text-black dark:text-white mb-4">
            Welcome, {user}!
          </h1>
          <p className="text-neutral-700 dark:text-neutral-300 mb-8">
            You are successfully logged in.
          </p>
          <button
            onClick={handleLogout}
            className="w-full max-w-xs py-3 px-4 rounded-lg font-semibold text-lg bg-white dark:bg-neutral-800 text-red-500 border border-red-500 transition-colors duration-200 hover:bg-red-50 dark:hover:bg-neutral-700"
          >
            Sign Out
          </button>
        </div>
      </div>
    </AuthGuard>
  );
}
