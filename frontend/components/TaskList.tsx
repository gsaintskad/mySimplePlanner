"use client";

import { useAppSelector } from "@/store/hooks";
import { Task } from "@/lib/types";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

// Helper to render stars
const renderStars = (importance: number) => {
  return (
    <div className="flex text-yellow-400">
      {Array.from({ length: 5 }, (_, i) => (
        <span key={i}>{i < importance ? "★" : "☆"}</span>
      ))}
    </div>
  );
};

// This is the individual Task component that triggers a modal
function TaskCard({ task }: { task: Task }) {
  return (
    <Dialog>
      <DialogTrigger asChild>
        <Card className="cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors">
          <CardHeader>
            <CardTitle className="text-lg">{task.title}</CardTitle>
            <CardDescription>{task.topic}</CardDescription>
          </CardHeader>
          <CardFooter>
            <div className="flex justify-between items-center w-full">
              <span className="text-sm text-muted-foreground">
                View Details
              </span>
              {renderStars(task.importance)}
            </div>
          </CardFooter>
        </Card>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{task.title}</DialogTitle>
          <DialogDescription>
            <strong>Topic:</strong> {task.topic}
          </DialogDescription>
        </DialogHeader>
        <div className="py-4 space-y-4">
          <div>
            <h4 className="font-medium mb-1">Description</h4>
            <p className="text-sm text-muted-foreground">
              {task.description || "No description provided."}
            </p>
          </div>
          <div>
            <h4 className="font-medium mb-1">Importance</h4>
            {renderStars(task.importance)}
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

// This component lists all tasks
export function TaskList() {
  const tasks = useAppSelector((state) => state.tasks.tasks);

  if (tasks.length === 0) {
    return (
      <Card className="mt-6">
        <CardContent className="pt-6">
          <p className="text-center text-muted-foreground">
            No tasks found. Click "New Task" to create one!
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="mt-6 space-y-4">
      <h2 className="text-2xl font-semibold">Your Tasks</h2>
      {tasks.map((task) => (
        <TaskCard key={task.id} task={task} />
      ))}
    </div>
  );
}
