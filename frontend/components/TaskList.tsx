"use client";

import { useMemo, useState } from "react";
import { useGetTasksQuery, useDeleteTaskMutation } from "@/store/authApi";
import { Task } from "@/lib/types";
import {
  Card,
  CardContent,
  CardDescription,
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
import { StarRating } from "@/components/ui/star-rating";
import { TaskFilters, TaskFiltersState } from "@/components/TaskFilters";
import { Loader2, Calendar, Pencil, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { NewTaskModal } from "./NewTaskForm";

function TaskCard({ task }: { task: Task }) {
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [deleteTask, { isLoading: isDeleting }] = useDeleteTaskMutation();

  const handleDelete = async (e: React.MouseEvent) => {
    e.stopPropagation();
    if (confirm("Are you sure you want to delete this task?")) {
      try {
        await deleteTask(task.id).unwrap();
      } catch (err) {
        console.error("Failed to delete task:", err);
      }
    }
  };

  return (
    <Dialog>
      <DialogTrigger asChild>
        <Card className="cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors">
          <CardHeader>
            <div className="flex justify-between items-start">
              <CardTitle className="text-lg">{task.title}</CardTitle>
              <StarRating
                value={task.priority}
                readOnly
                starClassName="size-4"
              />
            </div>
            <CardDescription className="flex items-center gap-1">
              <Calendar className="h-3 w-3" />
              Due: {new Date(task.due_date).toLocaleDateString()}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground line-clamp-2">
              {task.description || "No description."}
            </p>
          </CardContent>
        </Card>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <div className="flex justify-between items-start pr-8">
            <div>
              <DialogTitle>{task.title}</DialogTitle>
              <DialogDescription>
                Created: {new Date(task.created_at).toLocaleString()}
              </DialogDescription>
            </div>
            <div className="flex gap-2">
              <NewTaskModal
                taskToEdit={task}
                open={isEditOpen}
                setOpen={setIsEditOpen}
                trigger={
                  <Button variant="outline" size="sm">
                    <Pencil className="h-4 w-4 mr-2" /> Edit
                  </Button>
                }
              />
              <Button
                variant="destructive"
                size="sm"
                onClick={handleDelete}
                disabled={isDeleting}
              >
                {isDeleting ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Trash2 className="h-4 w-4" />
                )}
              </Button>
            </div>
          </div>
        </DialogHeader>
        <div className="py-4 space-y-4">
          <div>
            <h4 className="font-medium mb-1">Description</h4>
            <p className="text-sm text-muted-foreground whitespace-pre-wrap">
              {task.description || "No description provided."}
            </p>
          </div>
          <div className="flex justify-between items-center">
            <div>
              <h4 className="font-medium mb-1">Priority</h4>
              <StarRating value={task.priority} readOnly />
            </div>
            <div className="text-right">
              <h4 className="font-medium mb-1">Due Date</h4>
              <p className="text-sm">
                {new Date(task.due_date).toLocaleString()}
              </p>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

export function TaskList() {
  const { data: allTasks = [], isLoading, error } = useGetTasksQuery();
  const [filters, setFilters] = useState<TaskFiltersState>({
    rating: "all",
    sort: "newest",
  });

  const filteredTasks = useMemo(() => {
    let tasks = [...allTasks];
    if (filters.rating !== "all") {
      tasks = tasks.filter((t) => t.priority === parseInt(filters.rating));
    }
    tasks.sort((a, b) => {
      const dateA = new Date(a.created_at).getTime();
      const dateB = new Date(b.created_at).getTime();
      return filters.sort === "newest" ? dateB - dateA : dateA - dateB;
    });
    return tasks;
  }, [allTasks, filters]);

  if (isLoading)
    return (
      <div className="flex justify-center py-10">
        <Loader2 className="h-8 w-8 animate-spin text-blue-500" />
      </div>
    );
  if (error)
    return (
      <div className="text-center py-10 text-red-500">
        Failed to load tasks.
      </div>
    );

  return (
    <div className="mt-6 space-y-4">
      <h2 className="text-2xl font-semibold">Your Tasks</h2>
      <TaskFilters
        filters={filters}
        onFiltersChange={(f) => setFilters((prev) => ({ ...prev, ...f }))}
      />
      {filteredTasks.length === 0 ? (
        <Card>
          <CardContent className="pt-6 text-center text-muted-foreground">
            No tasks found.
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-4">
          {filteredTasks.map((t) => (
            <TaskCard key={t.id} task={t} />
          ))}
        </div>
      )}
    </div>
  );
}
