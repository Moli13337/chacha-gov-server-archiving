<template>
	<div class="appropriation-container">
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
					<div>拨款公示公告</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="content">
			<div class="text-content">
				<div class="title-container">
					<div class="title-box">
						<p class="title">{{appropriationData.name}}</p>
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
						<p v-if="appropriationData.province_name || appropriationData.city_name || appropriationData.district_name">
							<span>地区：</span>
							{{appropriationData.province_name}}{{appropriationData.city_name}}{{appropriationData.district_name}}
						</p>
						<p v-if="appropriationData.pub_time">
							<span>发布时间：</span>
							{{appropriationData.pub_time | formatDate}}
						</p>
					</div>
					<el-divider></el-divider>
				</div>
				<div class="text-container">
					<p class="text type"><el-divider direction="vertical"></el-divider>公示详情</p>
					<div class="detail-content"><pre class="pre-content">{{appropriationData.content}}</pre></div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import {
	APPROVAL_DETIAL
} from '@/utils/urls.js';
import collection from '@/utils/collection.js';
export default {
	scrollToTop: true,
	mixins: [collection],
	async asyncData({query, $axios}) {
		const result = await $axios.get(APPROVAL_DETIAL, {params: {id: query.id}});

		return {
			appropriationData: result || {},
			collection_obj_type: result.obj_type,
			collection_enc_id: result.enc_id
		};
	},
	methods: {
		// 初始化isCollection
		initIsCollection() {
			this.isCollection = this.appropriationData.collections_count;
		},
	},
	mounted() {
		this.initIsCollection();
	}

};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
@import '~assets/css/common.less';
.appropriation-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow:0px 0px 5px rgba(0,0,0,0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width:5px;
      height:18px;
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
        font-size:31px;
        font-family:Microsoft YaHei;
        font-weight:400;
        margin: auto;
        // width: 656px;
        color: #3B3B3B;
        padding: 20px;
        text-align: center;
      }
      .tips {
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        width: 656px;
        margin: auto;
        text-align: center;
        display: flex;
        justify-content: space-around;
      }
    }
    .type {
      font-size:19px;
      font-family:Microsoft YaHei;
      font-weight:400;
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
        line-height:25px;
      }
    .text-content {
      padding: 0 60px;
      color: @textColor;
      section {
        margin-bottom: 40px;
      }
    }

  }
  .detail-content {
    padding-bottom: 20px;
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
  .pre-content {
    word-break:break-all;
    white-space: pre-wrap;
    font-family:Microsoft YaHei;
  }
  }
}
</style>

