import {Component, OnInit} from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import {SnacksService} from "../../../snacks/services/snacks.service";
import {AlertService} from "../../../shared/services/alert.service";
import {AuthService} from "../../auth.service";
import {Router} from "@angular/router";

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
      private authService: AuthService,
      private alertService: AlertService,
      private router: Router
  ) {}

  ngOnInit(): void {

  }

  onSubmit() {
    if (this.form.valid) {
      console.log("sign in")
      const email = this.form.value.email!
      const password = this.form.value.password!
      this.authService.signIn(email, password).subscribe(resData => {
        this.authService.getUserData()
        this.router.navigate([''])
      }, error => {
        if(error.error.error === "Invalid credentials.") {
          this.showFailureAlert()
        }
      })
      }
    }

  showFailureAlert() {
  this.alertService.showAlertForTime("failureAlert", 2000)
  }
}
