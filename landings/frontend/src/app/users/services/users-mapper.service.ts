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
    let roles =  user.roles.join(', ')
    // switch (user.roles[0]) {
    //   case 'admin': {
    //     roles = 'Admin';
    //     break;
    //   }
    //   case 'office': {
    //     roles = 'Pracownik biurowy';
    //     break;
    //   }
    //   case 'warehouse': {
    //     roles = 'Pracownik magazynu';
    //     break;
    //   }
    //   case 'courier': {
    //     roles = 'Kurier';
    //     break;
    //   }
    // }
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
