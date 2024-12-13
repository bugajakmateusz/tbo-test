import { Injectable } from '@angular/core';
import { UserDisplayed } from '../components/models/user-displayed.model';
import { User } from '../components/models/user.model';

@Injectable({
  providedIn: 'root',
})
export class UsersMapperService {
  mapUserToUserDisplayed(user: User): UserDisplayed {
    const { id, username, firstName, lastName } = user;
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
    return { id, username, firstName, lastName, role };
  }
}
