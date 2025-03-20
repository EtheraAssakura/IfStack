import { Site } from './site';

export interface Location {
  id: number;
  name: string;
  site: Site;
  qr_code: string;
  description?: string;
  site_id: number;
} 