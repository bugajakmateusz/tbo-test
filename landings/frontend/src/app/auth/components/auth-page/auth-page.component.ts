import {Component, OnInit} from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import {SnacksService} from "../../../snacks/services/snacks.service";
import {AlertService} from "../../../shared/services/alert.service";
import {AuthService} from "../../auth.service";

@Component({
  selector: 'app-auth-page',
  templateUrl: './auth-page.component.html',
  styleUrls: ['./auth-page.component.scss']
})
export class AuthPageComponent implements OnInit{
  form = this.fb.group({
    email: ['', Validators.required],
    password: ['', Validators.required],
  });

  constructor(
      private fb: FormBuilder,
      private authService: AuthService
  ) {}

  ngOnInit(): void {

  }

  onSubmit() {
    if (this.form.valid) {
      console.log("sign in")
      const email = this.form.value.email!
      const password = this.form.value.password!
      this.authService.signIn(email, password).subscribe(resData => console.log(resData), error => console.log(error))
      this.form.reset()
    }
  }
}
