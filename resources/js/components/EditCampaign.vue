<template>
  <div class="row">
    <div class="offset-md-2 offset-sm-1 col-md-8 col-sm-10">
      <router-link to="/" class="d-block mb-3"
        >Back to campaign list</router-link
      >
      <div class="card card-default">
        <div class="card-header">
          <h3>Update Campaign - {{ title }}</h3>
        </div>
        <div class="card-body">
          <ValidationObserver v-slot="{ handleSubmit }">
            <form
              class="row g-3 needs-validation"
              novalidate
              @submit.prevent="handleSubmit(onSubmit)"
            >
              <validation-summary :validation-errors="validation_errors" />

              <div class="col-md-8">
                <label for="name" class="form-label">Name</label>
                <ValidationProvider
                  rules="required|max:255"
                  v-slot="{ errors, touched, invalid }"
                >
                  <input
                    v-model="name"
                    name="name"
                    type="text"
                    class="form-control"
                    :class="{
                      'is-invalid': touched && invalid,
                      'is-valid': touched && !invalid,
                    }"
                  />
                  <span class="invalid-feedback">{{ errors[0] }}</span>
                </ValidationProvider>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <label for="date_from" class="form-label">Date From</label>
                    <ValidationProvider
                      rules="required"
                      v-slot="{ errors, touched, invalid }"
                    >
                      <input
                        v-model="date_from"
                        name="date_from"
                        type="date"
                        class="form-control"
                        :class="{
                          'is-invalid': touched && invalid,
                          'is-valid': touched && !invalid,
                        }"
                      />
                      <span class="invalid-feedback">{{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>

                  <div class="col-md-6">
                    <label for="date_to" class="form-label">Date To</label>
                    <ValidationProvider
                      rules="required"
                      v-slot="{ errors, touched, invalid }"
                    >
                      <input
                        v-model="date_to"
                        name="date_to"
                        type="date"
                        class="form-control"
                        :class="{
                          'is-invalid': touched && invalid,
                          'is-valid': touched && !invalid,
                        }"
                      />
                      <span class="invalid-feedback">{{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <label for="daily_budget" class="form-label"
                      >Daily Budget</label
                    >
                    <ValidationProvider
                      rules="required"
                      v-slot="{ errors, touched, invalid }"
                    >
                      <input
                        v-model="daily_budget"
                        name="daily_budget"
                        type="number"
                        min="0"
                        class="form-control"
                        :class="{
                          'is-invalid': touched && invalid,
                          'is-valid': touched && !invalid,
                        }"
                      />
                      <span class="invalid-feedback">{{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>

                  <div class="col-md-6">
                    <label for="total_budget" class="form-label"
                      >Total Budget</label
                    >
                    <ValidationProvider
                      rules="required"
                      v-slot="{ errors, touched, invalid }"
                    >
                      <input
                        v-model="total_budget"
                        name="total_budget"
                        type="number"
                        min="0"
                        class="form-control"
                        :class="{
                          'is-invalid': touched && invalid,
                          'is-valid': touched && !invalid,
                        }"
                      />
                      <span class="invalid-feedback">{{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12">
                    <label for="banner_images" class="form-label"
                      >New Creative Banners
                      <small class="badge rounded-pill bg-secondary">{{
                        selected_image_banners.length
                      }}</small>
                    </label>
                    <div>
                      <span @click="selectFile" class="btn btn-info btn-sm"
                        >Select File(s)</span
                      >
                      <span @click="clearFile" class="btn btn-danger btn-sm"
                        >Clear All</span
                      >
                    </div>
                    <div class="d-none">
                      <ValidationProvider
                        ref="fileprovider"
                        rules="image"
                        v-slot="{}"
                      >
                        <input
                          ref="fileInput"
                          name="banner_images"
                          type="file"
                          @change="handleFileChange"
                          multiple
                          class="form-control"
                        />
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="col-md-12 mt-3">
                    <banner-file-previewer
                      :banner-files="selected_image_banners"
                    />
                  </div>
                  <div class="col-md-12 mt-3">
                    <label for="banner_images" class="form-label"
                      >Previous Creative Banners
                      <small class="badge rounded-pill bg-secondary">{{
                        previous_banners.length
                      }}</small>
                    </label>
                    <banner-file-previewer
                      :show-remove-button="true"
                      @removeBanner="onPreviousBannerRemoved"
                      :banner-files="previous_banners"
                    />
                  </div>
                </div>
              </div>
              <div class="col-12 mt-4">
                <div class="d-flex justify-content-end">
                  <input type="reset" ref="resetform" class="d-none" />
                  <button
                    class="btn btn-primary"
                    :disabled="is_loading"
                    type="submit"
                  >
                    Submit
                  </button>
                </div>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
      <router-link to="/" class="d-block mt-3"
        >Back to campaign list</router-link
      >
    </div>
  </div>
</template>

<script>
import { utils } from "../utils/utils";

export default {
  data() {
    return {
      id: "",
      name: "",
      date_from: "",
      date_to: "",
      total_budget: "",
      daily_budget: "",
      selected_images: [],
      selected_image_banners: [],
      previous_banners: [],
      deleted_banners: [],
      is_loading: false,
      validation_errors: [],
    };
  },
  created() {
    const { campaign } = this.$route.params;
    if (!campaign) this.goToHome();
    else {
      this.id = campaign.id;
      this.name = campaign.name;
      this.title= campaign.name;
      this.date_from = campaign.date_from;
      this.date_to = campaign.date_to;
      this.total_budget = campaign.total_budget;
      this.daily_budget = campaign.daily_budget;
      this.previous_banners = campaign.banners;
    }
  },
  methods: {
    goToHome() {
      this.$router.push({ name: "home" });
    },
    onPreviousBannerRemoved(id) {
      this.deleted_banners.push(id);
      this.previous_banners = this.previous_banners.filter((v) => v.id != id);
    },
    selectFile() {
      this.$refs.fileInput.click();
    },
    clearFile() {
      this.$refs.fileInput.value = [];
      this.selected_images = [];
      this.selected_image_banners = [];
    },
    async handleFileChange(e) {
      const self = this;
      const { valid, errors } = await self.$refs.fileprovider.validate(e);
      if (valid) {
        const files = e.target.files;
        Array.from(files).forEach((file) => {
          self.selected_images.push(file);
          self.selected_image_banners.push({
            image_url: URL.createObjectURL(file),
          });
        });
      } else {
        alert(errors.join(","));
      }
    },
    onSubmit() {
      const date_from = utils.format_date(this.date_from);
      const date_to = utils.format_date(this.date_to);

      const formdata = new FormData();
      // php hack
      formdata.append('_method','PUT')
      formdata.append("date_from", date_from);
      formdata.append("date_to", date_to);
      formdata.append("name", this.name);
      formdata.append("total_budget", this.total_budget);
      formdata.append("daily_budget", this.daily_budget);
      for (let i = 0; i < this.selected_images.length; i++) {
        formdata.append("banner_files[" + i + "]", this.selected_images[i]);
      }

      for (let i = 0; i < this.deleted_banners.length; i++) {
        formdata.append("deleted_files[" + i + "]", this.deleted_banners[i]);
      }

      this.is_loading = true;
      this.axios
        .post(`/api/campaign/update/${this.id}`, formdata)
        .then((resp) => {
          const data = resp.data;
          if (data.status) {
            alert(data.message || "Campaign updated successfully");
            this.goToHome();
          } else throw new Error(data.message);
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.validation_errors = utils.get_validation_error(error);
          } else {
            alert(error.message || error);
          }
        })
        .finally(() => {
          this.is_loading = false;
        });
    },
  },
};
</script>