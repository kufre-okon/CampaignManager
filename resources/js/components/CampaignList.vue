<template>
  <div>
    <div class="pr-4 pl-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="text-center mb-4">Campaigns List</h3>
        <router-link to="/create" class="btn btn-sm btn-primary"
          >Create New Campaign</router-link
        >
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Date From</th>
          <th>Date To</th>
          <th>Daily Budget(USD)</th>
          <th>Total Budget(USD)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(campaign, index) in campaigns" :key="campaign.id">
          <td>{{ index + 1 }}</td>
          <td>{{ campaign.name }}</td>
          <td>{{ campaign.date_from }}</td>
          <td>{{ campaign.date_to }}</td>
          <td>{{ (campaign.daily_budget || 0).toFixed(2) }}</td>
          <td>{{ (campaign.total_budget || 0).toFixed(2) }}</td>
          <td>
            <router-link
              :to="{ name: 'edit', params: { campaign } }"
              class="btn btn-primary btn-sm"
              >Edit</router-link
            >
            <button
              class="btn btn-sm btn-secondary"
              @click="viewBanners(campaign)"
            >
              Creative Preview <span class="badge badge-pill bg-light text-dark">{{campaign.banners.length}}</span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <creative-previewer-modal v-show="showPreview" @close="showPreview = false">
      <template v-slot:header>
        <h3>
          <strong> Creative Banners - {{ selectedCampaign.name }} </strong>
        </h3>
      </template>
      <template v-slot:body>
        <banner-file-previewer
          :show-empty-list-message="true"
          :banner-files="selectedCampaign.banners || []"
        />
      </template>

      <template v-slot:footer> </template>
    </creative-previewer-modal>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showPreview: false,
      selectedCampaign: {},
      campaigns: [],
    };
  },
  created() {
    this.axios
      .get("/api/campaign/list")
      .then((resp) => {
        const data = resp.data;
        if (data.status) this.campaigns = data.payload;
        else throw new Error(data.message);
      })
      .catch((error) => {
        alert(error.message || error);
      });
  },
  methods: {
    viewBanners(campaign) {
      this.selectedCampaign = campaign;
      this.showPreview = true;
    },
  },
};
</script>