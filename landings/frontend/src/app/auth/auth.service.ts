import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ConfigService} from "../config.service";
import {UserAuth} from "./user-auth.model";
import {Subject} from "rxjs";
import {User} from "../users/models/user.model";

@Injectable({
  providedIn: 'root'
})
export class AuthService {
    user = new Subject<UserAuth>()
    constructor(private http: HttpClient, private configService: ConfigService) { }

  signIn(email: string, password: string){
  return this.http.post(`${this.configService.apiUrl}login`, {
      username: email,
      password: password
    })
  }

  getUserData() {
        this.http.get(`${this.configService.apiUrl}json-api/users?filter%5Bme%5D=1`)
            .subscribe((response: any) => {
                const userData = response.data[0]
                    const user: UserAuth = {
                        id: userData.id,
                        email: userData.attributes.email,
                        name: userData.attributes.name,
                        surname: userData.attributes.surname,
                        roles: userData.attributes.roles
                    }
                    this.user.next(user)
            })
  }
}
