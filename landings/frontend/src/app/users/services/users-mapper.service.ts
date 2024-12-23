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
    let role = '';
    switch (user.role) {
      case 'admin': {
        role = 'Admin';
        break;
      }
      case 'office': {
        role = 'Pracownik biurowy';
        break;
      }
      case 'warehouse': {
        role = 'Pracownik magazynu';
        break;
      }
      case 'courier': {
        role = 'Kurier';
        break;
      }
    }
    return { id, email, fullName, role: user.role };
  }

  mapUserFromApiToUser(userFromApi: UserFromApi): User {
    const id = userFromApi.id.toString()
    const email = userFromApi.attributes.email
    const firstName = userFromApi.attributes.name
    const lastName = userFromApi.attributes.surname
    const role = userFromApi.attributes.roles.join(', ')
    const password = 'password'
    return {id, firstName, lastName, role,email, password}
  }
}
