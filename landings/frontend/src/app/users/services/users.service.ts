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
  users: User[] = [];

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
    firstName: string,
    lastName: string,
    roles: string[]
  ) {

    this.http.patch(`${this.configService.apiUrl}json-api/users/${this.id}`,{
      data: {
        type: "users",
        attributes: {
          name: firstName,
          surname: lastName,
          roles: roles
        }
      }
    })
        .subscribe(data => this.updateServiceData()
        )
  }

  changePassword(password: string) {
    this.http.patch(`${this.configService.apiUrl}json-api/users/${this.id}`,{
      data: {
        type: "users",
        attributes: {
          password: password,
        }
      }
    })
        .subscribe(data =>     this.updateServiceData()
        )
  }

  changeEmail(email: string) {
    this.http.patch(`${this.configService.apiUrl}json-api/users/${this.id}`,{
      data: {
        type: "users",
        attributes: {
          email: email,
        }
      }
    })
        .subscribe(data =>     this.updateServiceData()
        )
  }

  deleteUser() {
    console.log(`delete user with ID: ${this.id}`);
  }

  addUser(
    email: string,
    password: string,
    firstName: string,
    lastName: string,
    roles: string[]
  ) {
    roles.push("ROLE_USER")
  this.http.post(`${this.configService.apiUrl}json-api/users`,{
    data: {
      type: "users",
      attributes: {
        email: email,
        password: password,
        name: firstName,
        surname: lastName,
        roles: roles
      }
    }
  })
      .subscribe(data => console.log(data))
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
