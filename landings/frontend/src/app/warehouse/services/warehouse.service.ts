import { Injectable } from '@angular/core';
import { WarehouseSnack } from '../models/warehouseSnack.model';

@Injectable({
  providedIn: 'root',
})
export class WarehouseService {
  snacks: WarehouseSnack[] = [
    {
      id: '1',
      name: 'some snack',
      amount: 12,
    },
    {
      id: '2',
      name: 'other snack',
      amount: 7,
    },
    {
      id: '3',
      name: 'one more snack',
      amount: 341,
    },
  ];

  getSnacks(): WarehouseSnack[] {
    return this.snacks;
  }
}
