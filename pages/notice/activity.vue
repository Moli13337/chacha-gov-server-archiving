<template>
	<div class="activity-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/notice">公示公告</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>活动公示公告</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="content">
			<div class="text-content">
				<div class="title-container">
					<div class="title-box">
						<p class="title">【活动通知】{{activeData.name}}</p>
						<div class="btn-box">
							<el-button
								type="primary"
								v-if="isCollection == 0"
								@click="handleCollection"
							>收藏</el-button>
							<el-button
								type="primary"
								plain
								v-if="isCollection > 0"
								@click="handleCancelCollection"
							>取消收藏</el-button>
						</div>
					</div>
					<div class="tips">
						<p>
							<span>适用地区：</span>
							{{activeData.province_name}}{{activeData.city_name}}{{activeData.district_name}}
						</p>
						<p v-if="activeData.pub_time">
							<span>发布时间：</span>
							{{activeData.pub_time | formatDate}}
						</p>
						<p v-if="activeData.gov_agen && activeData.gov_agen.length > 0">
							<span>发文体系：</span>
							<span>{{activeData.gov_agen[0].gov_agen_first_name}}-{{activeData.gov_agen[0].gov_agen_second_name}}</span>
						</p>
						<p v-if="activeData.validity_sdate">
							<span>公示时间：</span>
							{{validityDate}}
						</p>
					</div>
					<el-divider></el-divider>
				</div>
				<div class="text-container">
					<section>
						<p class="text type">
							<el-divider direction="vertical"></el-divider>内容详情
						</p>
						<p v-html="activeData.content"/>
					</section>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import {
	PUBLIC_ACTIVE_DETIAL
} from '@/utils/urls.js';
import BreadCrumb from '@/components/breadcrumb';
import fecha from 'fecha';
import collection from '@/utils/collection.js';
export default {
	mixins: [collection],
	scrollToTop: true,
	components: {
		BreadCrumb
	},
	data() {
		return {
		};
	},
	// 验证ID是否存在
	validate({query}) {
		return query.id !== undefined;
	},
	// 加载完活动详情后才显示页面
	async asyncData({query, $axios}) {
		const result = await $axios.get(PUBLIC_ACTIVE_DETIAL, {params: {id: query.id}});

		return {
			activeData: result || {},
			collection_enc_id: result.enc_id,
			collection_obj_type: result.obj_type
		};
	},
	computed: {
		// 有效期限
		validityDate() {
			// 后端给的是10位，秒级的时间戳，需要加3个0
			if (this.activeData.validity_sdate) {
				let startDate = fecha.format(
					Number(this.activeData.validity_sdate + '000'),
					'YYYY/MM/DD'
				);

				if (this.activeData.validity_edate) {
					let endDate = fecha.format(
						Number(this.activeData.validity_edate + '000'),
						'YYYY/MM/DD'
					);

					return startDate + '-' + endDate;
				} else {
					return startDate + '起生效';
				}
			}
			return '';
		}
	},
	methods: {
		// 初始化isCollection
		initIsCollection() {
			this.isCollection = this.activeData.collections_count;
		},
	},
	mounted() {
		this.initIsCollection();
	}
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
@import '~assets/css/common.less';
.activity-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width: 5px;
      height: 18px;
      background-color: @primaryColor;
    }
    .bread-crumb-tip {
      display: inline-block;
      height: 18px;
      line-height: 18px;
    }
  }
  .content {
    width: 100%;
    border: 1px solid @borderLine;
    margin-top: 20px;
    .text-container {
      min-height: 400px;
       a {
          text-decoration: underline;
          color: @primaryColor;
    }
    table {
      border-collapse: collapse;
      tr,th,td {
        border: 1px solid @borderLine;
      }
    }
    }
    .title-container {
      .title-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .btn-box {
          .el-button {
            width: 109px;
            height: 31px;
            border-radius: 0;
            text-align: center;
            padding: 0;
          }
        }
      }
      .title {
        font-size: 31px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        margin: auto;
        color: #3b3b3b;
        padding: 20px 20px;
        text-align: center;
      }
      .tips {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        width: 1000px;
        margin: auto;
        display: flex;
        justify-content: space-around;
      }
    }
    .type {
      font-size: 19px;
      font-family: Microsoft YaHei;
      font-weight: 400;
      padding: 10px 0;
      .el-divider--vertical {
        width: 6px;
        background: @primaryColor;
        height: 27px;
        margin-left: 0;
      }
    }
    .policy {
      color: @poliyItemColor;
      line-height: 25px;
    }
    .text-content {
      padding: 0 60px;
      color: @textColor;
      section {
        margin-bottom: 40px;
      }
    }
  }
}
</style>

