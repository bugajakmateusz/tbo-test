import { Injectable } from '@angular/core';
import { Snack } from '../models/snack/snack.model';

@Injectable({
  providedIn: 'root',
})
export class SnacksService {
  snacks: Snack[] = [
    {
      id: '1',
      name: 'some snack',
    },
    {
      id: '2',
      name: 'other snack',
    },
  ];

  action = '';
  id = '';
  constructor() {}

  editSnack(name: string) {
    console.log(`edit snack with ID: ${this.id}. New name: ${name}`);
  }

  deleteSnack() {
    console.log(`delete snack with ID: ${this.id}`);
  }

  addSnack(name: string, prices: any) {
    console.log(`add snack. Name: ${name}. Prices:`);
    console.log(prices);
  }

  getSnacks() {
    return this.snacks;
  }

  getSnack(id: string) {
    return this.snacks.filter((el: Snack) => el.id === id)[0];
  }

  getCurrentSnack() {
    return this.getSnack(this.id);
  }
}
