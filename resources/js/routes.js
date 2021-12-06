import CampaignList from './components/CampaignList.vue';
import CreateCampaign from './components/CreateCampaign.vue';
import EditCampaign from './components/EditCampaign.vue';

export const routes = [
    {
        name: 'home',
        path: '/',
        component: CampaignList
    },
    {
        name:'create',
        path:'/create',
        component: CreateCampaign
    },
    {
        name:'edit',
        path:'/edit',
        component: EditCampaign
    }
]