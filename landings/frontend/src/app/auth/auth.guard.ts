import {CanActivateFn, Router} from '@angular/router';
import {AuthService} from "./auth.service";
import {inject} from "@angular/core";

export const authGuard: CanActivateFn = (route, state) => {

  const authService = inject(AuthService);
  const router = inject(Router)
  const isLoggedIn = authService.isLoggedIn
  const roles = authService.userRoles.filter(role => role !== "ROLE_USER")
  console.log(`authGuard: url: ${state.url} isLoggedIn: ${isLoggedIn}, roles: ${roles}`)

  const adminRole = "ROLE_ADMIN"
  const officeRole = "ROLE_OFFICE_MANAGER"
  const warehouseRole = "ROLE_LOGISTIC_MANAGER"
  const courierRole = "ROLE_COURIER"

  if(state.url === '/') {
    return true // main page available for everyone
  }

  if(state.url.includes("auth")) {
    return !isLoggedIn // auth page available for not logged user
  }

  return isLoggedIn
};
