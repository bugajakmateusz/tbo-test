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
  columns = ['ID', 'Email', 'Nazwisko', 'Rola'];

  users: User[] = [];
  roles = userRoleOptions;

  displayedUsers: UserDisplayed[] = [];

  buttons = [
    { text: 'Edytuj', action: 'editUser' },
    { text: 'Zablokuj/Odblokuj', action: 'ban/unbanUser' },
  ];

  form = this.fb.group({
    email: ['', Validators.required],
    password: ['', Validators.required],
    firstName: ['', Validators.required],
    lastName: ['', Validators.required],
    role: ['', Validators.required],
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

  editMachine() {
    this.usersService.editUser(
      this.form.value.email!,
      this.form.value.password!,
      this.form.value.firstName!,
      this.form.value.lastName!,
      this.form.value.role!
    );
  }
  activateDeactivateMachine() {
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
      password: user.password,
      firstName: user.firstName,
      lastName: user.lastName,
      role: user.role,
    });
  }

  onCallbackCalled() {
    switch (this.usersService.action) {
      case 'editUser': {
        this.editMachine();
        break;
      }
      case 'ban/unbanUser': {
        this.activateDeactivateMachine();
      }
    }
  }
}
