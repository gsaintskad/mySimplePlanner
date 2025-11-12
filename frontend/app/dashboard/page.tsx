"use client";

import AuthGuard from "../../components/AuthGuard";
import { useAppSelector, useAppDispatch } from "../../store/hooks";
import { logOut } from "../../store/authSlice";
import { useRouter } from "next/navigation";
import { LogOut } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { NewTaskModal } from "@/components/NewTaskForm"; // Import the modal
import { TaskList } from "@/components/TaskList"; // Import the task list

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
      <div className="flex min-h-screen w-full flex-col items-center bg-neutral-100 dark:bg-black p-4">
        <div className="w-full max-w-2xl">
          {/* Header Card */}
          <Card className="w-full text-center">
            <CardHeader>
              <CardTitle className="text-3xl">
                Welcome, <span className="text-blue-500">{user}!</span>
              </CardTitle>
              <CardDescription>
                You are successfully logged in to your dashboard.
              </CardDescription>
            </CardHeader>
            <CardContent className="flex flex-col sm:flex-row gap-4 justify-center">
              {/* Add New Task Button/Modal */}
              <NewTaskModal />

              {/* Logout Button */}
              <Button
                onClick={handleLogout}
                variant="destructive"
                className="w-full sm:w-auto"
              >
                <LogOut className="mr-2 h-4 w-4" />
                Sign Out
              </Button>
            </CardContent>
          </Card>

          {/* Task List Component */}
          <TaskList />
        </div>
      </div>
    </AuthGuard>
  );
}
