import { Location } from './location';
import { Supply } from './supply';

export interface Stock {
  id: number;
  estimated_quantity: number;
  local_alert_threshold: number;
  location: Location;
  supply: Supply;
  isLowStock(): boolean;
} 