<template>
	<div class="butler-container">
		<el-row :gutter="20">
			<el-col :span="5">
				<div class="butler-item-container">
					<ul>
						<li
							class="title-item"
							:class="{'title-item-active': currentPath === item.pathName}"
							v-for="(item, index) in titleList"
							:key="index"
							@click="handleItemClick(index, item.pathName)"
						>
							<img
								v-if="currentPath === item.pathName"
								class="icon"
								:src="item.icon_active"
							>
							<img
								v-else
								class="icon"
								:src="item.icon"
							>
							{{item.name}}
						</li>
					</ul>
				</div>
			</el-col>
			<el-col :span="19">
				<div class="item-content">
					<nuxt-child/>
				</div>
			</el-col>
		</el-row>
	</div>
</template>
<script>
import {
	projectPushIcon,
	projectPushIcon_activve,
	optionCollect,
	optionCollect_active,
	problemFeedback,
	problemFeedback_active,
	supportInfo,
	supportInfo_active,
	infoManagement,
	infoManagement_active,
	industryConcer,
	industryConcer_active
} from '~/assets/images';
export default {
	data() {
		return {
			titleList: [
				{
					id: 1,
					icon: projectPushIcon,
					icon_active: projectPushIcon_activve,
					alias: 'push',
					pathName: '/butler',
					name: '项目推送',
				},
				{
					id: 2,
					icon: optionCollect,
					icon_active: optionCollect_active,
					alias: 'solicitation',
					pathName: '/butler/enterprise_collect',
					name: '企业意见征集',
				},
				{
					id: 3,
					icon: problemFeedback,
					icon_active: problemFeedback_active,
					alias: 'feedback',
					pathName: '/butler/problem_feedback',
					name: '企业问题反馈与处理',
				},
				{
					id: 4,
					icon: supportInfo,
					icon_active: supportInfo_active,
					alias: 'support',
					pathName: '/butler/support_info',
					name: '企业获得支持信息',
				},
				{
					id: 5,
					icon: infoManagement,
					icon_active: infoManagement_active,
					alias: 'info_manage',
					pathName: '/butler/enterprise_info',
					name: '企业信息管理',
				},
				{
					id: 6,
					icon: industryConcer,
					icon_active: industryConcer_active,
					alias: 'follow',
					pathName: '/butler/industry_concer',
					name: '关注的行业',
				},
			]
		};
	},
	computed: {
		currentPath() {
			return this.$route.path;
		}
	},
	methods: {
		handleItemClick(index, pathName) {
			this.$router.push({path: pathName});
		},
	},
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
  .butler-container {
    // display: flex;
    .butler-item-container {
        width:244px;
    }
    .item-content {
      margin-left: 20px;
      padding: 0 20px 20px 20px;
      // flex: 1;
      background:rgba(255,255,255,1);
      box-shadow:0px 0px 4px rgba(0,0,0,0.05);

    }
    .title-item {
      width:244px;
      height:59px;
      background:rgba(255,255,255,1);
      box-shadow:0px 0px 4px rgba(39,170,61,1);
      margin-bottom: 20px;
      font-size: 18px;
      display: flex;
      align-items: center;
      cursor: pointer;
    }
    .title-item-active {
      background-color: rgba(39,170,61,1);
      color: #FFFFFF;
      font-weight: 500;
    }
    .icon {
      width: 29px;
      height: 29px;
      margin: 0 20px;
    }
  }
</style>

