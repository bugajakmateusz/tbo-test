import { Injectable } from '@angular/core';
import { User } from '../components/models/user.model';

@Injectable({
  providedIn: 'root',
})
export class UsersService {
  users: User[] = [
    {
      id: '1',
      username: 'kryspin798',
      password: '123',

      firstName: 'Klaudiusz',
      lastName: 'Mękarski',
      role: 'admin',
    },
    {
      id: '2',
      username: 'karomiz063',
      password: '456',
      firstName: 'Karolina',
      lastName: 'Mizgała',
      role: 'courier',
    },
  ];

  action = '';
  id = '';
  constructor() {}

  editUser(
    username: string,
    password: string,
    firstName: string,
    lastName: string,
    role: string
  ) {
    console.log(
      `edit user with ID: ${this.id}. New username: ${username}. New password: ${password}. New first name: ${firstName}. New last name: ${lastName}. New role: ${role}`
    );
  }

  deleteUser() {
    console.log(`delete user with ID: ${this.id}`);
  }

  addUser(
    username: string,
    password: string,
    firstName: string,
    lastName: string,
    role: string
  ) {
    console.log(
      `add user. New username: ${username}. New password: ${password}. New first name: ${firstName}. New last name: ${lastName}. New role: ${role}`
    );
  }

  getUsers() {
    return this.users;
  }

  getUser(id: string) {
    return this.users.filter((el: User) => el.id === id)[0];
  }

  getCurrentUser() {
    return this.getUser(this.id);
  }
}
