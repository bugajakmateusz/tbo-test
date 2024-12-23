import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ConfigService} from "../config.service";

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  constructor(private http: HttpClient, private configService: ConfigService) { }

  signIn(email: string, password: string){
  return this.http.post(`${this.configService.apiUrl}login`, {
      username: email,
      password: password
    })
  }
}
