import Link from "next/link";
import { LayoutDashboard, LogIn, UserPlus } from "lucide-react";
import { Button } from "@/components/ui/button"; // Correct shadcn/ui import
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";

export default function Home() {
  return (
    <div className="flex min-h-screen w-full flex-col items-center justify-center bg-neutral-50 dark:bg-neutral-950 p-4 font-sans antialiased">
      <main className="flex w-full max-w-lg flex-col items-center text-center">
        {/* Header */}
        <h1 className="text-5xl font-bold text-black dark:text-white mb-6">
          mySmartPlanner
        </h1>
        <p className="text-xl text-neutral-600 dark:text-neutral-400 mb-12">
          Your tasks, simplified and connected.
        </p>

        {/* About Section (Card) */}
        <Card className="w-full text-left mb-10">
          <CardHeader>
            <CardTitle>About Us</CardTitle>
            <CardDescription>
              Productivity tools should be powerful, not complicated.
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-base text-neutral-700 dark:text-neutral-300 leading-relaxed">
              <strong>mySimplePlanner</strong> is born from this idea. We focus
              on a clean, fast, and simple interface to manage your tasks, so
              you can focus on what actually matters.
            </p>
            <p className="text-base text-neutral-700 dark:text-neutral-300 leading-relaxed">
              We're building features to integrate with tools like{" "}
              <span className="font-medium text-blue-500">Telegram</span>,
              bringing your tasks directly into your conversations.
            </p>
          </CardContent>
        </Card>

        {/* Call to Action Buttons */}
        <div className="w-full space-y-3">
          <Button asChild size="lg" className="w-full text-base">
            <Link href="/dashboard">
              <LayoutDashboard className="mr-2 h-5 w-5" />
              Go to Dashboard
            </Link>
          </Button>

          <div className="flex flex-col space-x-3 space-y-3">
            <Button
              asChild
              variant="outline"
              size="lg"
              className="w-full text-base"
            >
              <Link href="/login">
                <LogIn className="mr-2 h-5 w-5" />
                Sign In
              </Link>
            </Button>
            <Button
              asChild
              variant="outline"
              size="lg"
              className="w-full text-base"
            >
              <Link href="/register">
                <UserPlus className="mr-2 h-5 w-5" />
                Sign Up
              </Link>
            </Button>
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
