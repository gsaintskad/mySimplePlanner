import Link from "next/link";
import { CheckCircle, LogIn, UserPlus, LayoutDashboard } from "lucide-react";

export default function Home() {
  return (
    <div className="flex min-h-screen w-full flex-col items-center justify-center bg-neutral-100 dark:bg-black p-4 font-sans antialiased">
      <main className="flex w-full max-w-lg flex-col items-center text-center">
        {/* Header */}
        <h1 className="text-5xl font-bold text-black dark:text-white mb-6">
          mySimplePlanner
        </h1>
        <p className="text-xl text-neutral-600 dark:text-neutral-400 mb-12">
          Your tasks, simplified and connected.
        </p>

        {/* About Section */}
        <div className="w-full bg-white dark:bg-neutral-900 shadow-xl rounded-2xl p-8 sm:p-10 mb-10">
          <h2 className="text-2xl font-semibold text-black dark:text-white mb-4">
            About Us
          </h2>
          <p className="text-base text-neutral-700 dark:text-neutral-300 leading-relaxed">
            <strong>mySimplePlanner</strong> is born from the idea that
            productivity tools should be powerful yet unobtrusive. We focus on a
            clean, fast, and simple interface to manage your tasks, so you can
            focus on what actually matters.
          </p>
          <p className="text-base text-neutral-700 dark:text-neutral-300 leading-relaxed mt-4">
            We're currently building features to integrate directly with your
            favorite tools, including{" "}
            <span className="font-medium text-blue-500">Telegram</span>, to
            bring your tasks directly into your conversations.
          </p>
        </div>

        {/* Call to Action Buttons */}
        <div className="w-full space-y-4">
          <Link
            href="/dashboard"
            className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg font-semibold text-lg bg-black dark:bg-white text-white dark:text-black transition-opacity duration-200 hover:opacity-80"
          >
            <LayoutDashboard size={20} />
            Go to Dashboard
          </Link>

          <div className="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
            <Link
              href="/login"
              className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg font-semibold text-lg bg-white dark:bg-neutral-800 text-black dark:text-white border border-neutral-300 dark:border-neutral-700 transition-colors duration-200 hover:bg-neutral-50 dark:hover:bg-neutral-700"
            >
              <LogIn size={20} />
              Sign In
            </Link>
            <Link
              href="/register"
              className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg font-semibold text-lg bg-white dark:bg-neutral-800 text-black dark:text-white border border-neutral-300 dark:border-neutral-700 transition-colors duration-200 hover:bg-neutral-50 dark:hover:bg-neutral-700"
            >
              <UserPlus size={20} />
              Sign Up
            </Link>
          </div>
        </div>
      </main>

      <footer className="mt-16 text-center text-neutral-500 dark:text-neutral-600">
        <p>
          &copy; {new Date().getFullYear()} mySimplePlanner. All rights
          reserved.
        </p>
      </footer>
    </div>
  );
}
