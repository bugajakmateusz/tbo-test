import { Injectable } from '@angular/core';
import { User } from '../models/user.model';
import { HttpClient } from '@angular/common/http';
import {ConfigService} from "../../config.service";
import {SnacksMapperService} from "../../snacks/services/snacks-mapper.service";
import {UsersMapperService} from "./users-mapper.service";
import {map, Observable} from "rxjs";
// @ts-ignore
import {UserFromApi} from "../models/user-from-api.model";


@Injectable({
  providedIn: 'root',
})
export class UsersService {
  users: User[] = [
    {
      id: '1',
      email: 'kryspin798@gmail.com',
      password: '123',

      firstName: 'Klaudiusz',
      lastName: 'Mękarski',
      role: 'admin',
    },
    {
      id: '2',
      email: 'karomiz063@gmail.com',
      password: '456',
      firstName: 'Karolina',
      lastName: 'Mizgała',
      role: 'courier',
    },
  ];

  action = '';
  id = '';
  constructor(private http: HttpClient, private configService: ConfigService, private usersMapperService: UsersMapperService) {
    this.login()
    this.updateServiceData()
  }

  private login() {
    this.http
        .post('http://localhost:3100/api/login', {
          username: 'ebaranowski@onet.pl',
          password: 'tab-admin',
        })
        .subscribe((data) => {
          console.log(data);
        });
  }

  private updateServiceData() {
    this.getUsers().subscribe(usersFromApi => this.users = usersFromApi.map(userFromApi => this.usersMapperService.mapUserFromApiToUser(userFromApi)))
  }
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

  getUsers(): Observable<UserFromApi[]> {
    return this.http
        .get<any>(`${this.configService.apiUrl}json-api/users`)
        .pipe(
            map((response) => {
              if (response) {
                console.log(response.data)
                return response.data
              }
              return []; // If response is null return empty array for safety.
            })
        );
  }

  getUser(id: string) {
    return this.users.filter((el: User) => el.id === id)[0];
  }

  getCurrentUser() {
    return this.getUser(this.id);
  }
}
