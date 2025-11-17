"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAppSelector } from "../store/hooks";

export default function AuthGuard({ children }: { children: React.ReactNode }) {
  // const isAuthenticated = true;
  const isAuthenticated = useAppSelector((state) => state.auth.isAuthenticated);
  const router = useRouter();

  useEffect(() => {
    if (!isAuthenticated) {
      router.replace("/login");
    }
  }, [isAuthenticated, router]);

  if (!isAuthenticated) {
    // You can return a loading spinner here
    return (
      <div className="flex min-h-screen w-full items-center justify-center bg-neutral-100 dark:bg-black">
        <p className="text-black dark:text-white">Loading...</p>
      </div>
    );
  }

  return <>{children}</>;
}
