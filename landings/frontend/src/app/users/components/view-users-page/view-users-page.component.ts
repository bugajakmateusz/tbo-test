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
    { text: 'Zablokuj', action: 'ban/unbanUser' },
  ];

  form = this.fb.group({
    firstName: ['', Validators.required],
    lastName: ['', Validators.required],
    roles: [[''], Validators.required],
  });

  emailForm = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
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
    this.getUsers()
  }

  getUsers() {
    this.usersService.getUsers().subscribe(usersFromApi => {
      this.users = usersFromApi.map(userFromApi => this.usersMapperService.mapUserFromApiToUser(userFromApi))
      this.displayedUsers = this.users.map((el) =>
          this.usersMapperService.mapUserToUserDisplayed(el)
      );    });
  }

  editUser() {
    if(this.form.valid) {
      this.usersService.editUser(
          this.form.value.firstName!,
          this.form.value.lastName!,
          this.form.value.roles!
      );
      setTimeout(()=>     this.getUsers()
        ,200)
    }
  }

  changeEmail() {
    if(this.emailForm.valid) {
      this.usersService.changeEmail(this.emailForm.value.email!)
      setTimeout(()=>     this.getUsers()
        ,200)
    }
  }

  changePassword() {
    if(this.passwordFrom.valid) {
      this.usersService.changePassword(this.passwordFrom.value.password!)
      setTimeout(()=>     this.getUsers()
        ,200)
    }
  }


  banUnbanUser() {
    this.usersService.deleteUser();
    setTimeout(()=>     this.getUsers()
    ,200)
  }

  onActionChosen(event: { id: string; action: string }) {
    this.usersService.action = event.action;
    this.usersService.id = event.id;
    this.setFormValuesToSelectedItem();
  }

  setFormValuesToSelectedItem() {
    const user = this.usersService.getCurrentUser();
    this.form.setValue({
      firstName: user.firstName,
      lastName: user.lastName,
      roles: user.roles,
    });
    this.passwordFrom.setValue({
      password: ""
    })
    this.emailForm.setValue({
      email: user.email
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
