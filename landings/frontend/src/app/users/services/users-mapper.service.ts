import { Injectable } from '@angular/core';
import { UserDisplayed } from '../models/user-displayed.model';
import { User } from '../models/user.model';
// @ts-ignore
import {UserFromApi} from '../models/user-from-api.model';
@Injectable({
  providedIn: 'root',
})
export class UsersMapperService {
  mapUserToUserDisplayed(user: User): UserDisplayed {
    const { id, email } = user;
    const fullName = `${user.firstName} ${user.lastName}`;
    let roles =  user.roles.filter((role: string) => role !== "ROLE_USER").map((role: string) => {
      switch (role) {
        case 'ROLE_ADMIN': {
          return 'Admin';
        }
        case 'ROLE_OFFICE_MANAGER': {
          return 'Pracownik biurowy';
        }
        case 'ROLE_LOGISTIC_MANAGER': {
          return 'Pracownik magazynu';
        }
        case 'ROLE_COURIER': {
          return 'Kurier';
        }
        default: {
          return role
        }
      }
    }).join(', ')

    return { id, email, fullName, roles };
  }

  mapUserFromApiToUser(userFromApi: UserFromApi): User {
    const id = userFromApi.id.toString()
    const email = userFromApi.attributes.email
    const firstName = userFromApi.attributes.name
    const lastName = userFromApi.attributes.surname
    const roles = userFromApi.attributes.roles
    const password = 'password'
    return {id, firstName, lastName, roles ,email, password}
  }
}
