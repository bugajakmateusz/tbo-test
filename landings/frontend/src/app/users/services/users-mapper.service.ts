import { Injectable } from '@angular/core';
import { UserDisplayed } from '../models/user-displayed.model';
import { User } from '../models/user.model';

@Injectable({
  providedIn: 'root',
})
export class UsersMapperService {
  mapUserToUserDisplayed(user: User): UserDisplayed {
    const { id, username } = user;
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
    return { id, username, fullName, role };
  }
}
