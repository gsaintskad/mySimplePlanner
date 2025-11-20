export interface Task {
  id: number;
  user_id: number;
  title: string;
  description: string;
  priority: number;
  due_date: string;
  created_at: string;
}
