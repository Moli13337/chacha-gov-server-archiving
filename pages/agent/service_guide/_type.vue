<template>
	<div class="person-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/agent">中介服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					{{guideTitle}}
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div>
			<el-row :gutter="20">
				<el-col :span="5">
					<div
						class="tips"
					>
						<ul class="tip-container">
							<li class="tip-item item-first"><p>服务指南</p><img
								src="~assets/images/icon_guid.png"
								class="icon-person"
							></li>
							<li
								v-for="(item, index) in guideList"
								:key="index"
								class="tip-item item-second"
								@click="handleGuideClick(item)"
								:class="{ 'item-active': guideType === item.type}"
							>{{item.name}}</li>
						</ul>
					</div>
				</el-col>
				<el-col :span="19">
					<detail
						:detail="guideDetail"
						tip="暂无数据"
					/>
				</el-col>
			</el-row>
		</div>
	</div>
</template>
<script>
import {
	AGENTSETUP_DETAIL
} from '@/utils/urls';
import Detail from '@/components/detail';
export default {
	components: {
		Detail
	},
	async asyncData({$axios}) {
		const result = await $axios.get(AGENTSETUP_DETAIL);
		const details = result || {};

		return {
			details
		};
	},
	data() {
		return {
			guideList: [
				{
					type: 1,
					name: '中介服务简介'
				},
				{
					type: 2,
					name: '入驻流程'
				},
				{
					type: 3,
					name: '评价机制'
				},
				{
					type: 4,
					name: '服务监督'
				},
			]
		};
	},
	computed: {
		guideType() {
			return parseInt(this.$route.params && this.$route.params.type || 1);
		},
		guideTitle() {
			let guide = this.guideList.find(item => item.type === this.guideType);

			return guide && guide.name || '';
		},
		guideDetail() {
			let detail = this.details.find(item => item.type === this.guideType);

			return detail || {};
		}
	},
	methods: {
		// 处理服务点击
		handleGuideClick(item) {
			this.$router.push({
				name: 'agent-service_guide-type',
				params: {type: item.type}
			});
		},
	}
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
.person-container {
  width: 100%;
  .tip-container {
    background: url('~assets/images/bg-agent-tip.png');
    background-size: 100% 100%;

  }
  .tip-item {
    width: 100%;
    padding: 30px 30px 30px 35px;
    font-size:16px;
    font-family:Microsoft YaHei;
    font-weight:400;
    color: @backGroundColor;
    cursor: pointer;
  }
  .item-active {
    background:#086EBF;
  }
  .item-first {
    font-size:20px;
    font-family:Microsoft YaHei;
    font-weight:bold;
    color: @backGroundColor;
    display: flex;
    justify-content: flex-start;
    .icon-person {
      width: 25px;
      height: 25px;
      margin-left: 30px;
    }
  }

  .pagination {
    text-align: center;
    padding: 20px 0;
    .number {
      width: 40px;
      height: 40px;
      line-height: 40px;
    }
    .btn-prev, .btn-next {
      height: 40px;
      border:1px solid rgba(235,235,235,1);
      background: none;
      padding: 0 10px;
    }
    .more {
      height: 40px;
      line-height: 40px;
    }
  }
}
</style>

