import { Component } from '@angular/core';
import { UserDisplayed } from '../../models/user-displayed.model';
import { User } from '../../models/user.model';
import { FormBuilder, Validators } from '@angular/forms';
import { UsersService } from '../../services/users.service';
import { UsersMapperService } from '../../../users/services/users-mapper.service';
import { userRoleOptions } from '../../userRoleOptions';

@Component({
  selector: 'app-view-users-page',
  templateUrl: './view-users-page.component.html',
  styleUrls: ['./view-users-page.component.scss'],
})
export class ViewUsersPageComponent {
  columns = ['ID', 'Email', 'Nazwisko', 'Role'];

  users: User[] = [];
  roles = userRoleOptions;

  displayedUsers: UserDisplayed[] = [];

  buttons = [
    { text: 'Edytuj', action: 'editUser' },
    { text: 'Zablokuj/Odblokuj', action: 'ban/unbanUser' },
  ];

  form = this.fb.group({
    email: ['', Validators.required],
    firstName: ['', Validators.required],
    lastName: ['', Validators.required],
    roles: [[''], Validators.required],
  });

  passwordFrom = this.fb.group({
    password: ['', Validators.required],
  });

  constructor(
    private fb: FormBuilder,
    private usersService: UsersService,
    private usersMapperService: UsersMapperService
  ) {}

  ngOnInit() {
    this.usersService.getUsers().subscribe(usersFromApi => {
      this.users = usersFromApi.map(userFromApi => this.usersMapperService.mapUserFromApiToUser(userFromApi))
      console.log("users", this.users)
      this.displayedUsers = this.users.map((el) =>
          this.usersMapperService.mapUserToUserDisplayed(el)
      );    });
    console.log("displayedUsers", this.displayedUsers)


  }

  editUser() {
    if(this.form.valid) {
      this.usersService.editUser(
          this.form.value.email!,
          this.form.value.firstName!,
          this.form.value.lastName!,
          this.form.value.roles!
      );
    }
  }

  changePassword() {
    if(this.passwordFrom.valid) {
      this.usersService.changePassword(this.passwordFrom.value.password!)
    }
  }
  banUnbanUser() {
    this.usersService.deleteUser();
  }

  onActionChosen(event: { id: string; action: string }) {
    this.usersService.action = event.action;
    this.usersService.id = event.id;
    this.setFormValuesToSelectedItem();
  }

  setFormValuesToSelectedItem() {
    const user = this.usersService.getCurrentUser();
    this.form.setValue({
      email: user.email,
      firstName: user.firstName,
      lastName: user.lastName,
      roles: user.roles,
    });
    this.passwordFrom.setValue({
      password: ""
    })
  }

  onCallbackCalled() {
    switch (this.usersService.action) {
      case 'ban/unbanUser': {
        this.banUnbanUser();
      }
    }
  }
}
