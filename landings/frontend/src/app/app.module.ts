import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

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
import { ChangePricesPageComponent } from './machines/components/change-prices-page/change-prices-page.component';
import { MachineReportPageComponent } from './reports/components/machine-report-page/machine-report-page.component';
import { WarehouseReportPageComponent } from './reports/components/warehouse-report-page/warehouse-report-page.component';
import { BuySellReportPageComponent } from './reports/components/buy-sell-report-page/buy-sell-report-page.component';
import { ViewUsersPageComponent } from './users/components/view-users-page/view-users-page.component';
import { AddUserPageComponent } from './users/components/add-user-page/add-user-page.component';
import { PageLayoutComponent } from './shared/components/page-layout/page-layout.component';
import { LoginPageComponent } from './login/components/login-page/login-page.component';
import { FormComponent } from './shared/form/form.component';
import { FormInputComponent } from './shared/components/form-input/form-input.component';
import { FormButtonComponent } from './shared/components/form-button/form-button.component';
import { FormTextareaComponent } from './shared/components/form-textarea/form-textarea.component';
import { FormSelectComponent } from './shared/components/form-select/form-select.component';

const appRoutes: Routes = [
  { path: '', component: MainPageComponent },
  { path: 'login', component: LoginPageComponent },
  {
    path: 'warehouse',
    children: [
      { path: 'view', component: ViewWarehousePageComponent },
      { path: 'delivery', component: DeliveryPageComponent },
      { path: 'hand-to-courier', component: HandToCourierPageComponent },
    ],
  },
  {
    path: 'snacks',
    children: [
      { path: 'view', component: ViewSnacksPageComponent },
      { path: 'add', component: AddSnackPageComponent },
    ],
  },
  {
    path: 'machines',
    children: [
      { path: 'view', component: ViewMachinesPageComponent },
      { path: 'add', component: AddMachinePageComponent },
      { path: 'change-prices', component: ChangePricesPageComponent },
    ],
  },
  {
    path: 'reports',
    children: [
      { path: 'machines', component: MachineReportPageComponent },
      { path: 'warehouse', component: WarehouseReportPageComponent },
      { path: 'buy-sell', component: BuySellReportPageComponent },
    ],
  },
  {
    path: 'users',
    children: [
      { path: 'view', component: ViewUsersPageComponent },
      { path: 'add', component: AddUserPageComponent },
    ],
  },
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
    ChangePricesPageComponent,
    MachineReportPageComponent,
    WarehouseReportPageComponent,
    BuySellReportPageComponent,
    ViewUsersPageComponent,
    AddUserPageComponent,
    PageLayoutComponent,
    LoginPageComponent,
    FormComponent,
    FormInputComponent,
    FormButtonComponent,
    FormTextareaComponent,
    FormSelectComponent,
  ],
  imports: [BrowserModule, AppRoutingModule, RouterModule.forRoot(appRoutes)],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
