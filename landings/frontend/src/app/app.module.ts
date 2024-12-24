import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NavbarComponent } from './shared/components/navbar/navbar.component';
import { RouterModule, Routes } from '@angular/router';
import { AddSnackPageComponent } from './snacks/components/add-snack-page/add-snack-page.component';
import { ViewSnacksPageComponent } from './snacks/components/view-snacks-page/view-snacks-page.component';
import { MainPageComponent } from './shared/components/main-page/main-page.component';
import { ViewWarehousePageComponent } from './warehouse/components/view-warehouse-page/view-warehouse-page.component';
import { DeliveryPageComponent } from './warehouse/components/delivery-page/delivery-page.component';
import { HandToCourierPageComponent } from './warehouse/components/hand-to-courier-page/hand-to-courier-page.component';
import { ViewMachinesPageComponent } from './machines/components/view-machines-page/view-machines-page.component';
import { AddMachinePageComponent } from './machines/components/add-machine-page/add-machine-page.component';
import { MachineReportPageComponent } from './reports/components/machine-report-page/machine-report-page.component';
import { WarehouseReportPageComponent } from './reports/components/warehouse-report-page/warehouse-report-page.component';
import { ViewUsersPageComponent } from './users/components/view-users-page/view-users-page.component';
import { AddUserPageComponent } from './users/components/add-user-page/add-user-page.component';
import { PageLayoutComponent } from './shared/components/page-layout/page-layout.component';
import { FormComponent } from './shared/form/form.component';
import { FormInputComponent } from './shared/components/form-text-input/form-input.component';
import { FormButtonComponent } from './shared/components/form-button/form-button.component';
import { FormTextareaComponent } from './shared/components/form-textarea/form-textarea.component';
import { FormSelectComponent } from './shared/components/form-select/form-select.component';
import { TableComponent } from './shared/components/table/table.component';
import { ModalComponent } from './shared/components/modal/modal.component';
import { ObjectToArrayPipe } from './shared/pipes/object-to-array.pipe';
import { AbstractControlToFormControlPipe } from './shared/pipes/abstract-control-to-form-control.pipe';
import { AlertComponent } from './shared/components/alert/alert.component';
import { AuthPageComponent } from './auth/components/auth-page/auth-page.component';
import {authGuard} from "./auth/auth.guard";
import { FormMultiselectComponent } from './shared/components/form-multiselect/form-multiselect.component';
import { BuyReportPageComponent } from './reports/components/buy-report-page/buy-report-page.component';
import { SellReportPageComponent } from './reports/components/sell-report-page/sell-report-page.component';

const appRoutes: Routes = [
  { path: '', component: MainPageComponent, canActivate: [authGuard] },
  {
    path: 'warehouse',
    children: [
      { path: 'view', component: ViewWarehousePageComponent, canActivate: [authGuard] },
      { path: 'delivery', component: DeliveryPageComponent, canActivate: [authGuard] },
      { path: 'hand-to-courier', component: HandToCourierPageComponent, canActivate: [authGuard] },
    ],
  },
  {
    path: 'snacks',
    children: [
      { path: 'view', component: ViewSnacksPageComponent, canActivate: [authGuard] },
      { path: 'add', component: AddSnackPageComponent, canActivate: [authGuard] },
    ],
  },
  {
    path: 'machines',
    children: [
      { path: 'view', component: ViewMachinesPageComponent, canActivate: [authGuard] },
      { path: 'add', component: AddMachinePageComponent, canActivate: [authGuard] },
    ],
  },
  {
    path: 'reports',
    children: [
      { path: 'machines', component: MachineReportPageComponent, canActivate: [authGuard] },
      { path: 'warehouse', component: WarehouseReportPageComponent, canActivate: [authGuard] },
      { path: 'buy', component: BuyReportPageComponent, canActivate: [authGuard] },
      { path: 'sell', component: SellReportPageComponent, canActivate: [authGuard] },
    ],
  },
  {
    path: 'users',
    children: [
      { path: 'view', component: ViewUsersPageComponent, canActivate: [authGuard] },
      { path: 'add', component: AddUserPageComponent, canActivate: [authGuard] },
    ],
  },
  {
    path: 'auth',
    component: AuthPageComponent, canActivate: [authGuard]
  }
];

@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    AddSnackPageComponent,
    ViewSnacksPageComponent,
    ViewWarehousePageComponent,
    DeliveryPageComponent,
    MainPageComponent,
    HandToCourierPageComponent,
    ViewMachinesPageComponent,
    AddMachinePageComponent,
    MachineReportPageComponent,
    WarehouseReportPageComponent,
    ViewUsersPageComponent,
    AddUserPageComponent,
    PageLayoutComponent,
    FormComponent,
    FormInputComponent,
    FormButtonComponent,
    FormTextareaComponent,
    FormSelectComponent,
    TableComponent,
    ModalComponent,
    ObjectToArrayPipe,
    AbstractControlToFormControlPipe,
    AlertComponent,
    AuthPageComponent,
    FormMultiselectComponent,
    BuyReportPageComponent,
    SellReportPageComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    RouterModule.forRoot(appRoutes),
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
  ],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
