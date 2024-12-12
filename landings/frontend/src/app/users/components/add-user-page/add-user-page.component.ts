import { Component } from '@angular/core';

@Component({
  selector: 'app-add-user-page',
  templateUrl: './add-user-page.component.html',
  styleUrls: ['./add-user-page.component.scss'],
})
export class AddUserPageComponent {
  userRoleOptions = [
    {
      name: 'Admin',
      value: 'admin',
    },
    {
      name: 'Pracownik biurowy',
      value: 'office',
    },
    {
      name: 'Pracownik magazynu',
      value: 'warehouse',
    },
    {
      name: 'Kurier',
      value: 'courier',
    },
  ];
}
