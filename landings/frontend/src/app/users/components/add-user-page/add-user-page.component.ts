import { Component } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { userRoleOptions } from '../../userRoleOptions';
import { UsersService } from '../../services/users.service';

@Component({
  selector: 'app-add-user-page',
  templateUrl: './add-user-page.component.html',
  styleUrls: ['./add-user-page.component.scss'],
})
export class AddUserPageComponent {
  roles = userRoleOptions;

  form = this.fb.group({
    username: [''],
    password: [''],
    firstName: [''],
    lastName: [''],
    role: [''],
  });

  constructor(private fb: FormBuilder, private usersService: UsersService) {}

  onSubmit() {
    this.usersService.addUser(
      this.form.value.username!,
      this.form.value.password!,
      this.form.value.firstName!,
      this.form.value.lastName!,
      this.form.value.role!
    );
  }
}
