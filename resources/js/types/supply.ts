export interface StockItem {
  location_id: number;
  location_name: string;
  estimated_quantity: number;
  local_alert_threshold: number;
}

export interface Supply {
  id: number;
  name: string;
  reference: string;
  description?: string;
  unit: string;
  category: string;
  stock_items: StockItem[];
} 