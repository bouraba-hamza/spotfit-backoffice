import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {MatButtonModule} from '@angular/material/button';
import {MatTableModule} from '@angular/material/table';
import {MatPaginatorModule} from '@angular/material/paginator';
import {MatIconModule} from '@angular/material/icon';
import {MatChipsModule} from '@angular/material/chips';
import {MatMenuModule} from '@angular/material/menu';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ToastrModule, ToastrService } from 'ngx-toastr';
import { SharedModule } from '@app/shared/shared.module';
import {PartnerListComponent} from "@app/modules/dashboard/partners/partner-list/partner-list.component";
import {PartnerFormComponent} from "@app/modules/dashboard/partners/partner-form/partner-form.component";
import {PartnersRoutingModule} from "@app/modules/dashboard/partners/partners-routing.module";

@NgModule({
  declarations: [PartnerListComponent, PartnerFormComponent],
  imports: [
    CommonModule,
    PartnersRoutingModule,
    FormsModule,
    ReactiveFormsModule,

    // Material
    MatButtonModule,
    MatTableModule,
    MatPaginatorModule,
    MatIconModule,
    MatChipsModule,
    MatMenuModule,

    // Toastr
    ToastrModule.forRoot(),

    // Shared
    SharedModule,
  ],
  providers: [
    { provide: ToastrService } 
  ]
})
export class PartnersModule { }
